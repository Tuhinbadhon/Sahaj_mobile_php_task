# 📱 Sahaj Mobile - EMI Management Dashboard

> Laravel-based customer EMI management system for mobile phone financing in Bangladesh.

## ✨ Key Features

- 📊 **Customer Dashboard** - 50+ EMI records with 13 columns
- 🔍 **Search & Filter** - By name/phone and status
- ⬆️⬇️ **Smart Sorting** - Sort by ID/Date/Applicant
- 📄 **Pagination** - 10/25/50/100 per page
- 📤 **CSV Export** - Download with Bengali (৳) support
- 🎨 **Status Badges** - Color-coded (🟢🟡🔴🔵⚫)
- �� **No Database** - JSON storage

## 🚀 Quick Installation

```bash
# Clone the repository
git clone https://github.com/Tuhinbadhon/Sahaj_mobile_php_task
cd Sahaj_mobile_php_task

# Install dependencies
composer install

# Create directories & set permissions
mkdir -p storage/framework/{sessions,views,cache} storage/logs
chmod -R 775 storage bootstrap/cache

# Start server
php artisan serve

# Open: http://127.0.0.1:8000
```

> **Note**: The `.env` file is included in the repository for easy setup. Just clone and run!
> chmod -R 775 storage bootstrap/cache

# Start server

php artisan serve

# Open: http://127.0.0.1:8000

```

## 📋 Requirements

- PHP >= 8.1
- Composer
- Laravel 11.x

## 📖 Usage

**Search**: Enter name/phone → Click "Search"
**Filter**: Select status dropdown
**Sort**: Click column headers
**Paginate**: Choose records per page
**Export**: Click "Export All to CSV"

## 📊 Data Columns

| Column         | Description         |
| -------------- | ------------------- |
| ID             | Customer identifier |
| Originate Date | EMI start date      |
| Duration       | Payment period      |
| Package        | Phone model         |
| Applicant      | Customer name       |
| Telephone      | Phone number        |
| Shop Name      | Location            |
| Total Amount   | Full price (৳)      |
| Installment    | Monthly payment (৳) |
| Paid           | Amount paid (৳)     |
| Due            | Balance (৳)         |
| Last Payment   | Payment date        |
| Status         | 🟢🟡🔴🔵⚫          |

**Status**: �� Active | 🟡 Pending | 🔴 Overdue | 🔵 Completed | ⚫ Rejected

## 🔧 Structure

```

task/
├── app/Http/Controllers/CustomerController.php # Main logic
├── routes/web.php # Routes
├── resources/views/dashboard.blade.php # UI
├── storage/data/OUTPUT.json # 50+ records
└── public/css/dashboard.css # Styles

````

## 💻 Technology

- **Backend**: Laravel 11.x, PHP 8.1+
- **Frontend**: Bootstrap 5.3.0, Icons
- **Data**: JSON (no database)

## 🎨 Customization

**Default page size** (`CustomerController.php` line 79):

```php
$perPage = (int)$request->get('per_page', 25);
````

**Add data**: Edit `storage/data/OUTPUT.json`  
**Branding**: Edit `.env` → `APP_NAME="Your Company"`

## 🐛 Troubleshooting

**Server won't start**

```bash
php -v && php artisan cache:clear && php artisan serve
```

**No data**

```bash
ls -la storage/data/OUTPUT.json
chmod 664 storage/data/OUTPUT.json
```

**Permissions**

```bash
chmod -R 775 storage bootstrap/cache
```

**CSV issues**

```bash
php artisan route:list | grep export
```

**500 error**

```bash
# .env: APP_DEBUG=true
tail -f storage/logs/laravel.log
```

## 📊 Sample Data

50 records in `storage/data/OUTPUT.json`

**Distribution**: Active 35% | Pending 15% | Overdue 20% | Completed 20% | Rejected 10%

**Example**:

```json
{
  "id": 1234,
  "originate_date": "2025-01-15",
  "emi_package": "Samsung Galaxy A54",
  "applicant": "Md. Rahman Khan",
  "telephone": "01712345678",
  "shop_name": "Sahaj Mobile - Dhaka",
  "total_amount": 30000.0,
  "installment_display": "৳2,500",
  "paid": 5000.0,
  "due": 25000.0,
  "status": "Active"
}
```

## 🔑 Key Files

- `CustomerController.php`: index() & exportCSV() methods
- `routes/web.php`: GET / and /export-csv
- `dashboard.blade.php`: Main view
- `OUTPUT.json`: Customer data

## ✅ Checklist

✅ 13 columns | ✅ Search/Filter | ✅ Sort | ✅ Pagination  
✅ CSV export | ✅ Status badges | ✅ Bengali (৳) | ✅ No DB

## 🌐 Browsers

✅ Chrome | ✅ Firefox | ✅ Edge | ✅ Safari

## 🚀 Commands

```bash
php artisan serve          # Start
php artisan cache:clear    # Clear cache
php artisan route:list     # Routes
composer dump-autoload     # Reload
```

## 📄 License

© 2025 Sahaj Mobile

---

**💡 Help**: Check logs (`tail -f storage/logs/laravel.log`) | F12 Console | `php -v`

**Created with ❤️ for Sahaj Mobile**

# Sahaj_mobile_php_task
