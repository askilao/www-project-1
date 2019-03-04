# Prosjekt 1 IMT2291 våren 2019 #


# Prosjektdeltakere #
Askil A. Olsen,
Anders G. Gustad & Espen Skuggerud


# Start docker environment #
**All docker related files is in docker/ folder**
To run docker-compose up | down cd into docker/ folder `cd docker/`

Edit the *docker-compose.yml* file with prefered ports for applications.
run `sudo docker-compose up -d` and all containers will start.
May get error if port 80 is used, just edit port for www container.
All web documents (php, html, css etc) goes in www/ folder.
All tests go in test/ folder

NB:
If database does not update it may be because of some persistant storage
to fix you can run
`docker rm $(docker ps -a -q)` This will delete all containers (you dont need them)
`docker volume rm $(docker volume ls -q)` This will delete all volumes created
`docker system prune` might also work, dont know tho

To enter a container run `sudo docker-compose exec <SERVICE_NAME> bash` and it will give a root shell inside the container.
NB: When entering test container your working directory is the www/ folder, so to access test folder do `cd ../tests`.
Service name can be:.
* www (apache server)
* test (codeception container)
* phpmyadmin (phpmyadmin container)
* selenium-hub (selenium config container)
* chrome (chrome container for selenium)
* firefox (firefox container for selenium)

*all is accessible at localhost:PORT_OF_SERVICE*.
If any change is made to the Dockerfile you need to build to apply changes
* `sudo docker-compose up -d --build` should do the trick.


