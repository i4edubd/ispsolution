#!/bin/bash

################################################################################
# ISP Solution - Complete Installation Script for Ubuntu
# 
# This script installs and configures all dependencies required for the ISP
# Solution on a fresh Ubuntu VM, including:
# - System packages and dependencies
# - PHP 8.2+ and extensions
# - Composer
# - Node.js and NPM
# - MySQL 8.0
# - Redis
# - Nginx web server
# - FreeRADIUS server
# - OpenVPN server (optional)
# - Laravel application setup
# - Database configuration
#
# Usage: sudo bash install.sh
################################################################################

################################################################################
# ISP Solution - Ultimate Production Installer v4.0
# Features: Auth-Aware Deep Clean, Presence Detection, SSL, RADIUS, & Cron
################################################################################

set -e 

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

# --- Configuration ---
DOMAIN_NAME=${DOMAIN_NAME:-"radius.ispbills.com"}
EMAIL=${EMAIL:-"admin@ispbills.com"}
INSTALL_DIR="/var/www/ispsolution"
DB_NAME="ispsolution"
CRED_FILE="/root/ispsolution-credentials.txt"

# --- Utility Functions ---
print_status() { echo -e "${BLUE}[INFO]${NC} $1"; }
print_done() { echo -e "${GREEN}[SUCCESS]${NC} $1"; }
print_error() { echo -e "${RED}[ERROR]${NC} $1"; }

check_root() {
    if [ "$EUID" -ne 0 ]; then
        print_error "Please run as root (sudo bash install.sh)"
        exit 1
    fi
}

# --- Step 0: MySQL Auth Verification (Prevents Crashes) ---
verify_mysql_access() {
    print_status "Verifying MySQL Access..."
    if mysql -u root -e "status" >/dev/null 2>&1; then
        MYSQL_CONN="mysql -u root"
    else
        echo -e "${YELLOW}[!] MySQL root password is required to handle existing data.${NC}"
        read -s -p "Enter current MySQL ROOT password: " EXISTING_PASS
        echo ""
        if mysql -u root -p"${EXISTING_PASS}" -e "status" >/dev/null 2>&1; then
            MYSQL_CONN="mysql -u root -p${EXISTING_PASS}"
        else
            print_error "MySQL Access Denied. Cannot proceed."
            exit 1
        fi
    fi
}

# --- Step 1: Deep Clean (Remove Leftovers) ---
deep_clean() {
    echo -e "${YELLOW}Proceeding with Deep Clean...${NC}"
    systemctl stop nginx php8.2-fpm freeradius mysql 2>/dev/null || true

    print_status "Wiping application files..."
    [ -d "$INSTALL_DIR" ] && rm -rf "$INSTALL_DIR"

    print_status "Dropping old databases and users..."
    $MYSQL_CONN -e "DROP DATABASE IF EXISTS $DB_NAME; DROP DATABASE IF EXISTS radius; DELETE FROM mysql.user WHERE User='ispsolution' OR User='radius'; FLUSH PRIVILEGES;" || true

    print_status "Cleaning configs..."
    rm -f /etc/nginx/sites-enabled/ispsolution /etc/nginx/sites-available/ispsolution
    rm -f /etc/freeradius/3.0/mods-enabled/sql
    
    print_done "System is now a blank slate."
}

# --- Step 2: Presence Detection with Timeout ---
check_existing() {
    # Detect directory OR existing database
    if [ -d "$INSTALL_DIR" ] || $MYSQL_CONN -e "use $DB_NAME" >/dev/null 2>&1; then
        echo -e "${RED}!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!${NC}"
        echo -e "${RED}   WARNING: EXISTING INSTALLATION DETECTED          ${NC}"
        echo -e "${RED}!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!${NC}"
        echo -e "An existing installation of ISP Solution was found."
        echo ""
        echo -e "1) Remove and Install Fresh (DEEP CLEAN) - DEFAULT"
        echo -e "2) Cancel Installation"
        echo ""
        
        USER_CHOICE=1
        echo -n "Select [1/2] (Auto-proceed Choice 1 in 30s): "
        read -t 30 USER_CHOICE || USER_CHOICE=1
        echo ""

        if [ "$USER_CHOICE" == "2" ]; then
            print_error "Installation cancelled."
            exit 0
        fi
        deep_clean
    fi
}

# --- Step 3: Install Core Stack ---
install_stack() {
    print_status "Installing LEMP Stack and Dependencies..."
    apt-get update -y
    apt-get install -y software-properties-common curl git unzip openssl ufw mysql-server certbot python3-certbot-nginx bc
    add-apt-repository ppa:ondrej/php -y && apt-get update -y
    apt-get install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-intl nginx
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
}

# --- Step 4: Credential Generation ---
setup_creds_and_db() {
    print_status "Generating new secure credentials..."
    NEW_ROOT_PASS=$(openssl rand -base64 12 | tr -d '=+/')
    NEW_APP_PASS=$(openssl rand -base64 12 | tr -d '=+/')
    NEW_RAD_PASS=$(openssl rand -base64 15 | tr -d '=+/')

    # Update MySQL Root Password
    $MYSQL_CONN -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${NEW_ROOT_PASS}';"
    
    # Define connection for new credentials
    NEW_CONN="mysql -u root -p${NEW_ROOT_PASS}"

    # Create DBs
    $NEW_CONN -e "CREATE DATABASE $DB_NAME; CREATE USER 'ispsolution'@'localhost' IDENTIFIED BY '$NEW_APP_PASS'; GRANT ALL PRIVILEGES ON $DB_NAME.* TO 'ispsolution'@'localhost';"
    $NEW_CONN -e "CREATE DATABASE radius; CREATE USER 'radius'@'localhost' IDENTIFIED BY '$NEW_RAD_PASS'; GRANT ALL PRIVILEGES ON radius.* TO 'radius'@'localhost'; FLUSH PRIVILEGES;"

    # Save to file
    {
        echo "MYSQL_ROOT_PASS: $NEW_ROOT_PASS"
        echo "APP_DB_PASS:     $NEW_APP_PASS"
        echo "RADIUS_DB_PASS:  $NEW_RAD_PASS"
    } > "$CRED_FILE"
    chmod 600 "$CRED_FILE"
    
    # Store locally for script use
    RAD_DB_PASS_LOCAL="$NEW_RAD_PASS"
    APP_DB_PASS_LOCAL="$NEW_APP_PASS"
    ROOT_DB_PASS_LOCAL="$NEW_ROOT_PASS"
}

# --- Step 5: RADIUS Setup ---
setup_radius() {
    print_status "Configuring FreeRADIUS..."
    apt-get install -y freeradius freeradius-mysql
    ln -sf /etc/freeradius/3.0/mods-available/sql /etc/freeradius/3.0/mods-enabled/
    
    SQL_MOD="/etc/freeradius/3.0/mods-enabled/sql"
    sed -i 's/driver = "rlm_sql_null"/driver = "rlm_sql_mysql"/' "$SQL_MOD"
    sed -i "s/^[[:space:]]*#[[:space:]]*login = .*/login = \"radius\"/" "$SQL_MOD"
    sed -i "s/^[[:space:]]*#[[:space:]]*password = .*/password = \"$RAD_DB_PASS_LOCAL\"/" "$SQL_MOD"
    sed -i "s/^[[:space:]]*#[[:space:]]*radius_db = .*/radius_db = \"radius\"/" "$SQL_MOD"

    mysql -u root -p"${ROOT_DB_PASS_LOCAL}" radius < /etc/freeradius/3.0/mods-config/sql/main/mysql/schema.sql
    systemctl restart freeradius
}

# --- Step 6: Laravel Deployment ---
setup_laravel() {
    print_status "Cloning application and setting up environment..."
    git clone https://github.com/i4edubd/ispsolution.git "$INSTALL_DIR"
    cd "$INSTALL_DIR"
    cp .env.example .env

    # Sync .env
    sed -i "s|DB_DATABASE=.*|DB_DATABASE=$DB_NAME|" .env
    sed -i "s|DB_USERNAME=.*|DB_USERNAME=ispsolution|" .env
    sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$APP_DB_PASS_LOCAL|" .env
    
    # Add Radius Keys
    for K in RADIUS_DB_DATABASE RADIUS_DB_USERNAME RADIUS_DB_PASSWORD RADIUS_DB_HOST; do
        grep -q "$K" .env || echo "$K=" >> .env
    done
    sed -i "s|RADIUS_DB_HOST=.*|RADIUS_DB_HOST=127.0.0.1|" .env
    sed -i "s|RADIUS_DB_DATABASE=.*|RADIUS_DB_DATABASE=radius|" .env
    sed -i "s|RADIUS_DB_USERNAME=.*|RADIUS_DB_USERNAME=radius|" .env
    sed -i "s|RADIUS_DB_PASSWORD=.*|RADIUS_DB_PASSWORD=$RAD_DB_PASS_LOCAL|" .env

    composer install --no-dev --optimize-autoloader
    php artisan key:generate
    php artisan migrate --force
    php artisan db:seed --force
    
    # Set Permissions
    chown -R www-data:www-data "$INSTALL_DIR"
    chmod -R 775 storage bootstrap/cache

    # Add Cron
    (crontab -l 2>/dev/null | grep -v "schedule:run" ; echo "* * * * * cd $INSTALL_DIR && php artisan schedule:run >> /dev/null 2>&1") | crontab -
}

# --- Step 7: Web & SSL ---
setup_web_ssl() {
    print_status "Configuring Nginx and SSL..."
    cat > /etc/nginx/sites-available/ispsolution <<EOF
server {
    listen 80;
    server_name $DOMAIN_NAME;
    root $INSTALL_DIR/public;
    index index.php;
    location / { try_files \$uri \$uri/ /index.php?\$query_string; }
    location ~ \.php$ { include snippets/fastcgi-php.conf; fastcgi_pass unix:/var/run/php/php8.2-fpm.sock; }
}
EOF
    ln -sf /etc/nginx/sites-available/ispsolution /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default
    systemctl restart nginx

    # SSL (Optional: Fail gracefully if DNS is not set)
    certbot --nginx -d "$DOMAIN_NAME" --non-interactive --agree-tos -m "$EMAIL" --redirect || echo "SSL skipped (Check DNS A record)."
}

# --- Step 8: Final Verification ---
run_sanity_check() {
    echo -e "\n${YELLOW}=== FINAL SYSTEM CHECK ===${NC}"
    cd "$INSTALL_DIR"
    php artisan tinker --execute="DB::connection()->getPdo(); print('Main DB: OK\n');" 2>/dev/null || print_error "Main DB: FAIL"
    php artisan tinker --execute="DB::connection('radius')->getPdo(); print('Radius DB: OK\n');" 2>/dev/null || print_error "Radius DB: FAIL"
    systemctl is-active --quiet freeradius && print_done "RADIUS: OK"
    echo -e "${YELLOW}==========================${NC}\n"
}

# --- Execution ---
main() {
    check_root
    verify_mysql_access
    check_existing
    install_stack
    setup_creds_and_db
    setup_radius
    setup_laravel
    setup_web_ssl
    run_sanity_check
    print_done "Installation complete! Credentials stored in $CRED_FILE"
}

main "$@"
