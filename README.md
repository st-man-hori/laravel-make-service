# Create Service Class And Interface File Command For Laravel

## Install
```bash
$ composer require st-man-hori/laravel-make-service --dev
```

## Usage
```bash
php artisan make:service {name}
```

### Example
```bash
$ php artisan make:service User
```

```php
Services/Userservice.php

<?php

namespace App\Services;

use App\Services\UserServiceInterface;

class UserService implements UserServiceInterface
{
  
}
```

```php
Services/UserserviceInterface.php

<?php

namespace App\Services;

interface UserServiceInterface
{
  
}
```