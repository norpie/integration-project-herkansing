# Integration Project

## Structure

```tree
.
├── compose.yml
├── fossbilling
│   ├── compose.yml
│   ├── data
│   │   └── library
│   │       ├── Box
│   │       │   └── EventManager.php
│   │       └── Hook
│   │           ├── composer.json
│   │           ├── composer.lock
│   │           └── SyncHook.php
│   └── micro
│       ├── Dockerfile
│       ├── fossbilling.go
│       ├── go.mod
│       ├── go.sum
│       ├── main.go
│       └── models.go
├── rabbitmq
│   └── compose.yml
├── README.md
└── wordpress
    ├── compose.yml
    └── themes
        └── userdata
            ├── composer.json
            ├── composer.lock
            ├── functions.php
            ├── index.php
            └── style.css
```

## Progress

- Client
    - Create
        - [x] Wordpress
        - [x] Fossbilling
    - Update
        - [x] Wordpress
        - [x] Fossbilling
    - Delete
        - [x] Wordpress
        - [x] Fossbilling

- Bugs
    - Creating an account in wordpress does not give Fossbilling an address
    - Updating an account in Fossbilling removes the address from Wordpress
    - Cannot change email, since it is the primary key in Fossbilling

## Services

### Linux

* username: `vboxuser`
* password: `changeme`

### Fossbilling

#### Web

* username: `admin@userdata.local`
* password: `password`
* api: `PWVhmHzfAwk4y6yDJS6QCJ2kRwZFL4fJ`
* port: `8080`

#### Microservice

Is responsible for listening to RabbitMQ events and sending them to the Fossbilling API

#### Hook

Is responsible for listening to Fossbilling events and sending them to RabbitMQ

### RabbitMQ

* username: `user`
* password: `password`
* port `15672`

Is responsible for handling the queue of events

### Wordpress

* username: `admin`
* password: `hW5@WWK)EOvP2WNHNI`
* port: `8000`

#### Theme - Userdata

- `functions.php` is responsible for all the "backend" logic of the theme.
- `style.css` is responsible for the theme's style.
- `index.php` is responsible for the theme's main page. It is a simple page that displays the users' data, and allows updating/deleting them.

## Sources

- Initial Wordpress rabbitmq plugin: [Claude.AI](https://claude.ai)
- General help with [Github Copilot](https://copilot.github.com)
