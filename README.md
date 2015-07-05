Yii2-comments
===================
Yii2 module for comments management

Installation
---
Run
```code
php composer.phar require "demi/comments" "~1.0"
```
or


Add to composer.json in your project
```json
{
	"require": {
  		"demi/comments": "~1.0"
	}
}
```
then run command
```code
php composer.phar update
```

# Configurations
---
Create `comments` table run:
```code
yii migrate --migrationPath=@vendor/demi/comments/console/migrations
```

# Usage
---
