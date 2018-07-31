# promo-audio-scrapper-example

This is API that searches [Promo video collection](https://slide.ly/promo/create?mode=search&keyword=fun&sort_order=most_popular) 
takes first of found videos, extracts audio, converts it to mp3 and exposes it for download. 

## Endpoint

```text
GET /api/promo2mp3?tag={search keyword here}
```

Result:

```json
{
  "videoId": "{some video id}",
  "downloadUrl": "{some download url}"
}
```

In case of error:

```json
{
  "error": true,
  "message": "{error message}"
}
```

Fatal errors return error code `500`, "soft" errors (search returned no results) return
status `200`, but still with above schema. Validation errors (missing or invalid tag
parameter) return code `400`.

## Install

Fetch repository from git or download zip, then in main directory run:

```bash
composer install
```

## Run

```bash
composer run
```

will boot up built in web server on local host, port 8000.

## Tests

```bash
vendor/bin/phpunit
```

