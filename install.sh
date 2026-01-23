#!/bin/bash

################################################################################
# ISP Solution - Complete Auto-Installation Script for Ubuntu VM
# 
# This script performs a COMPLETE CLEAN INSTALLATION on Ubuntu VM including:
#
# INSTALLATION PHASE:
# - System packages and dependencies
# - PHP 8.2+ and extensions
# - Composer
# - MySQL Server (default Ubuntu version)
# - Nginx web server
# - FreeRADIUS server
# - Laravel application setup
# - Database configuration
#
# Usage: sudo bash install.sh
################################################################################

################################################################################
# ISP Solution - Master Production Installer (v5.0)
# Features: Presence Detection, 2GB Swap, Deep Clean, SSL, RADIUS, & Laravel
################################################################################
set -e 

# --- Configuration ---
DOMAIN_NAME="radius.ispbills.com"
INSTALL_DIR="/var/www/ispsolution"
DB_NAME="ispsolution"
CRED_FILE="/root/ispsolution-credentials.txt"

print_status() { echo -e "\033[0;34m[INFO]\033[0m $1"; }
print_done() { echo -e "\033[0;32m[SUCCESS]\033[0m $1"; }

# 1. System Preparation
setup_system() {
    print_status "Installing Stack & Certbot..."
    apt-get update -y
    apt-get install -y software-properties-common curl git unzip ufw mysql-server freeradius freeradius-mysql bc cron certbot python3-certbot-nginx
    add-apt-repository ppa:ondrej/php -y && apt-get update -y
    apt-get install -y php8.2-fpm php8.2-cli php8.2-mysql php8.2-mbstring php8.2-curl php8.2-xml php8.2-bcmath php8.2-intl nginx
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
}

# 2. Nginx Configuration (Fixed Path)
setup_nginx() {
    print_status "Configuring Nginx for $DOMAIN_NAME..."
    mkdir -p /etc/nginx/sites-available /etc/nginx/sites-enabled
    
    cat > /etc/nginx/sites-available/ispsolution <<EOF
server {
    listen 80;
    server_name $DOMAIN_NAME;
    root $INSTALL_DIR/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
EOF
    ln -sf /etc/nginx/sites-available/ispsolution /etc/nginx/sites-enabled/
    rm -f /etc/nginx/sites-enabled/default
    systemctl restart nginx
}

# 3. Database & App Setup
setup_app() {
    N_ROOT_PASS=$(openssl rand -base64 12 | tr -d '=+/')
    N_APP_PASS=$(openssl rand -base64 12 | tr -d '=+/')
    N_RAD_PASS=$(openssl rand -base64 15 | tr -d '=+/')

    systemctl start mysql
    mysql -u root -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '${N_ROOT_PASS}'; FLUSH PRIVILEGES;"
    
    F_CONN="mysql -u root -p${N_ROOT_PASS}"
    $F_CONN -e "CREATE DATABASE IF NOT EXISTS $DB_NAME; CREATE DATABASE IF NOT EXISTS radius;"
    $F_CONN -e "CREATE USER IF NOT EXISTS 'ispsolution'@'localhost' IDENTIFIED BY '$N_APP_PASS';"
    $F_CONN -e "GRANT ALL PRIVILEGES ON $DB_NAME.* TO 'ispsolution'@'localhost';"
    $F_CONN -e "CREATE USER IF NOT EXISTS 'radius'@'localhost' IDENTIFIED BY '$N_RAD_PASS';"
    $F_CONN -e "GRANT ALL PRIVILEGES ON radius.* TO 'radius'@'localhost'; FLUSH PRIVILEGES;"

    print_status "Cloning and migrating..."
    rm -rf "$INSTALL_DIR" # Ensure path is clean for git
    git clone https://github.com/i4edubd/ispsolution.git "$INSTALL_DIR"
    cd "$INSTALL_DIR"
    cp .env.example .env

    sed -i "s|DB_PASSWORD=.*|DB_PASSWORD=$N_APP_PASS|" .env
    sed -i "s|RADIUS_DB_PASSWORD=.*|RADIUS_DB_PASSWORD=$N_RAD_PASS|" .env
    
    composer install --no-dev --optimize-autoloader
    php artisan key:generate
    php artisan migrate --force
    php artisan db:seed --force

    # RADIUS SQL
    ln -sf /etc/freeradius/3.0/mods-available/sql /etc/freeradius/3.0/mods-enabled/
    SQL_MOD="/etc/freeradius/3.0/mods-enabled/sql"
    sed -i "s/driver = .*/driver = \"rlm_sql_mysql\"/" "$SQL_MOD"
    sed -i "s/^[[:space:]]*#[[:space:]]*login = .*/login = \"radius\"/" "$SQL_MOD"
    sed -i "s/^[[:space:]]*#[[:space:]]*password = .*/password = \"$N_RAD_PASS\"/" "$SQL_MOD"
    sed -i "s/^[[:space:]]*#[[:space:]]*radius_db = .*/radius_db = \"radius\"/" "$SQL_MOD"
    
    $F_CONN radius < /etc/freeradius/3.0/mods-config/sql/main/mysql/schema.sql
    sed -i 's/[[:space:]]*-sql/sql/' /etc/freeradius/3.0/sites-enabled/default
    systemctl restart freeradius

    chown -R www-data:www-data "$INSTALL_DIR"
    (crontab -l 2>/dev/null | grep -v "schedule:run" ; echo "* * * * * cd $INSTALL_DIR && php artisan schedule:run >> /dev/null 2>&1") | crontab -
    
    echo -e "MYSQL_ROOT: $N_ROOT_PASS\nAPP_PASS: $N_APP_PASS\nRAD_PASS: $N_RAD_PASS" > "$CRED_FILE"
}

# --- Main Execution ---
main() {
    setup_system
    setup_nginx
    setup_app
    print_done "System Ready! Access: http://$DOMAIN_NAME"
    echo "To enable SSL later, run: certbot --nginx -d $DOMAIN_NAME"
}

main "$@"
