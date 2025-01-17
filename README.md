# Login with Rate Limiting (JavaScript && Php)
 Simple php login with request limit , ip ban feature

![assets/images/view.png](assets/images/view.png)

## Database theory

```
DATABASE: loginratelimiting

TABLE CLIENT:
    Id (Primary Key);
    Email (Varchar 100);
    Password (Varchar 255);
    RegisterDate (DateTime current_timestamp);

TABLE REQUEST
    Id (Primary Key);
    Ip (Varchar 20);
    RequestDate (Timestamp);

TABLE BLACKLIST
    Id (Primary Key);
    Ip (Varchar 20, Unique);
    UnbanDate (Timestamp);
```