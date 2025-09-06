# Project Setup


## Prerequisites

-   [Composer](https://getcomposer.org/) installed
-   [Docker](https://www.docker.com/) and [Docker
    Compose](https://docs.docker.com/compose/) installed
-   Unix-based shell (Linux/Mac) or WSL for Windows

------------------------------------------------------------------------

## Installation & Setup

1.  Install PHP dependencies:

    ``` bash
    composer install
    ```

2.  Build and start Docker containers:

    ``` bash
    sudo docker-compose up --build -d
    ```

3.  Make the test script executable (only required once):

    ``` bash
    chmod +x runtest.sh
    ```

4.  Run tests:

    ``` bash
    ./runtest.sh
