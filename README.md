# Laravel REST Hooks

Package that handles subscrition, modification, removal, and execution of REST Hooks.
See (https://resthooks.org/) for more information.

## How to install

```bash
composer require silver-co/laravel-rest-hooks
php artisan migrate
```

## Database schema:

## Usage:

Package registers resource controller for rest hooks manipulation.

```bash
GET /api/hooks
GET /api/hooks/{id}
POST /api/hooks
PUT|PATCH /api/hooks/{id}
DELETE /api/hooks
```

### Register to a hook:

```bash
POST /api/hooks
```

```json
{
  "event": "{entity}.{eventName}",
  "target": "https://example.com/webhook"
}
```

### Modify a subscription:

```bash
PUT /api/hooks/{id}
```

```json
{
  "event": "{entity}.{eventName}",
  "target": "https://example.com/webhook"
}
```

### Unsubscribe:

```bash
DELETE /api/hooks/{id}
```

### Get information about a specific subscription:

```bash
GET /api/hooks/{id}
```

### List all the subscriptions

By default all the subscription for a given user will be returned by the endpoint:

```bash
GET /api/hooks
```

Filtering by status:

```bash
GET /api/hooks?status=1
```

### Get a specific subscription

Retreiving a specific subscription can be done only by ID:

```bash
GET /api/hooks/{id}
```

### Firing RestHook with Data

```php
$data = [
    'foo' => 'bar'
];

$resthook = RestHook::find(1);
$resthook->dispatch($data);
```
