#!/bin/bash
# ============================================
# Quick Setup Script for GitHub Codespaces
# ============================================

echo "🎓 School Management System - Quick Setup"
echo "=========================================="
echo ""

# Colors
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Check PHP version
echo -e "${YELLOW}Checking PHP version...${NC}"
php_version=$(php -r 'echo PHP_VERSION;')
echo -e "${GREEN}✓ PHP $php_version detected${NC}"
echo ""

# Check required extensions
echo -e "${YELLOW}Checking PHP extensions...${NC}"
required_extensions=("pdo" "pdo_mysql" "mbstring" "json" "openssl")
missing_extensions=()

for ext in "${required_extensions[@]}"; do
    if php -m | grep -q "^$ext$"; then
        echo -e "${GREEN}✓ $ext${NC}"
    else
        echo -e "${RED}✗ $ext (missing)${NC}"
        missing_extensions+=("$ext")
    fi
done

if [ ${#missing_extensions[@]} -eq 0 ]; then
    echo -e "${GREEN}✓ All required extensions are installed${NC}"
else
    echo -e "${RED}Missing extensions: ${missing_extensions[*]}${NC}"
    echo "Install with: sudo apt-get install -y php-mysql php-mbstring"
fi
echo ""

# Database configuration
echo -e "${YELLOW}Database Configuration${NC}"
echo "Please provide your remote database credentials:"
echo ""

read -p "Database Host (e.g., mysql.example.com): " db_host
read -p "Database Name (default: school_management_db): " db_name
db_name=${db_name:-school_management_db}
read -p "Database User: " db_user
read -sp "Database Password: " db_pass
echo ""
echo ""

# Test database connection
echo -e "${YELLOW}Testing database connection...${NC}"

# Create test PHP script
cat > test_connection.php << EOF
<?php
try {
    \$conn = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", "$db_user", "$db_pass");
    \$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "SUCCESS";
} catch(PDOException \$e) {
    echo "ERROR: " . \$e->getMessage();
}
?>
EOF

result=$(php test_connection.php)
rm test_connection.php

if [[ $result == "SUCCESS" ]]; then
    echo -e "${GREEN}✓ Database connection successful!${NC}"
    echo ""
    
    # Update config.php
    echo -e "${YELLOW}Updating configuration...${NC}"
    
    # Backup original config
    if [ ! -f "config/database.php.backup" ]; then
        cp config/database.php config/database.php.backup
        echo -e "${GREEN}✓ Original config backed up${NC}"
    fi
    
    # Update database credentials in database.php
    sed -i "s/define('DB_HOST', '.*');/define('DB_HOST', '$db_host');/" config/database.php
    sed -i "s/define('DB_NAME', '.*');/define('DB_NAME', '$db_name');/" config/database.php
    sed -i "s/define('DB_USER', '.*');/define('DB_USER', '$db_user');/" config/database.php
    sed -i "s/define('DB_PASS', '.*');/define('DB_PASS', '$db_pass');/" config/database.php
    
    echo -e "${GREEN}✓ Configuration updated${NC}"
    echo ""
    
    # Import schema prompt
    echo -e "${YELLOW}Database Schema${NC}"
    read -p "Do you want to import the database schema now? (y/n): " import_schema
    
    if [[ $import_schema == "y" || $import_schema == "Y" ]]; then
        echo -e "${YELLOW}Importing schema...${NC}"
        mysql -h "$db_host" -u "$db_user" -p"$db_pass" "$db_name" < database/complete_schema.sql 2>&1
        
        if [ $? -eq 0 ]; then
            echo -e "${GREEN}✓ Schema imported successfully!${NC}"
        else
            echo -e "${RED}✗ Schema import failed. Please import manually.${NC}"
            echo "Command: mysql -h $db_host -u $db_user -p $db_name < database/complete_schema.sql"
        fi
    else
        echo "You can import the schema later using:"
        echo "mysql -h $db_host -u $db_user -p $db_name < database/complete_schema.sql"
    fi
    echo ""
    
    # Start server prompt
    echo -e "${YELLOW}Starting Development Server${NC}"
    read -p "Start PHP development server now? (y/n): " start_server
    
    if [[ $start_server == "y" || $start_server == "Y" ]]; then
        echo ""
        echo -e "${GREEN}Starting server on http://0.0.0.0:8000${NC}"
        echo "Press Ctrl+C to stop the server"
        echo ""
        echo -e "${YELLOW}Default login credentials:${NC}"
        echo "Username: superadmin"
        echo "Password: Admin@2026"
        echo ""
        php -S 0.0.0.0:8000 -t public
    fi
    
else
    echo -e "${RED}✗ Database connection failed!${NC}"
    echo "Error: $result"
    echo ""
    echo "Please check your credentials and try again."
    echo "You can manually edit: app/config/config.php"
    exit 1
fi
