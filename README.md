## Stateful API Layer

```
/api/{service}/{version}

    GET /state/{state}/{operation?}
    POST /{operation}/{state?}
```

```
POST /api/air/v1/search
GET  /api/air/v1/state/123/search
POST /api/air/v1/select/123
GET  /api/air/v1/state/234/select
POST /api/air/v1/book/234
GET  /api/air/v1/state/345/book
```

```php
use TTBooking\Stateful\Facades\Stateful;

Stateful::service('air')->query(fly()->from('MOW')->to('LED'));
$state = Stateful::service('air')->get('123', 'search');
```
