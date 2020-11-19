# Addventures: DevOps

The purpose of this project is to provide formal DevOps management for a set of
Drupal instances (platforms) and multisites (apps) owned by an organization.

## Install

Run the below from your host OSX terminal:

```
sh -c "$(curl -fsSL https://raw.github.com/addventures/devops/bin/install.sh)"
```

This process will:

1. Confirm local host dependencies such as composer.
1. Clone this project to ~/sys/project/devops
1. Run composer install for this project.
1. Symlink an `add` command to `/usr/local/bin` to call this project.
1. Run `sys:init` command to configure your local environment.

## Environments

### Local

#### Directory structure

The below directory structure is used to maintain your local environment
consistently.

Path | Purpose
--- | ---
~/sys/platform|Each Drupal instance runs out of a single directory here.
~/sys/project|Store git projects that are not Drupal instances, such as this.
~/sys/backup|Database and file backups generated by Drush and other CLI processes.
~/sys/etc|Configuration files used by this project.
~/sys/tmp|Temporary files used by this.

#### Docker/Docksal

Local Drupal environments run in a set of Docker containers managed by Docksal.

Docksal is a wrapper to Docker that abstracts much of its complexity to a
simple interface of developer commands.

Docksal provides a set of stack configurations aligned with several cloud
providers, including Acquia. So we use their [Acquia stack configuration](https://docs.docksal.io/stack/zero-configuration/).

In the output below:

1. `cvs-hub_*` containers provide a local instance of CVS Hub running out of: `~/sys/platform/cvs-hub`
1. `cvs-bob_*` containers provide a local instance of CVS Best of Brands running out of: `~/sys/platform/cvs-bob`
1. `docksal-*` containers provide generic management of all instances.

```
➜  devops git:(master) docker ps -a
CONTAINER ID        IMAGE                     COMMAND                  CREATED             STATUS                  PORTS                                                    NAMES
a0f110a0ec68        docksal/varnish:4.1-2.0   "docker-entrypoint.s…"   22 hours ago        Up 3 hours (healthy)    80/tcp, 6082/tcp                                         cvs-hub_varnish_1
89c7a84a9cdd        docksal/apache:2.4-2.3    "httpd-foreground"       22 hours ago        Up 3 hours (healthy)    80/tcp, 443/tcp                                          cvs-hub_web_1
c6c94ca6d903        docksal/cli:2.11-php7.3   "/opt/startup.sh sup…"   22 hours ago        Up 3 hours (healthy)    22/tcp, 3000/tcp, 9000/tcp                               cvs-hub_cli_1
4c0d9b5cb0fe        docksal/mysql:5.6-1.5     "docker-entrypoint.s…"   22 hours ago        Up 3 hours (healthy)    0.0.0.0:33064->3306/tcp                                  cvs-hub_db_1
85265b003733        memcached:1.4-alpine      "docker-entrypoint.s…"   22 hours ago        Up 3 hours              11211/tcp                                                cvs-hub_memcached_1
e70894054201        docksal/solr:1.0-solr4    "/opt/solr/bin/solr …"   22 hours ago        Up 3 hours              8983/tcp                                                 cvs-hub_solr_1
a0f110a0ec68        docksal/varnish:4.1-2.0   "docker-entrypoint.s…"   22 hours ago        Up 3 hours (healthy)    80/tcp, 6082/tcp                                         cvs-bob_varnish_1
89c7a84a9cdd        docksal/apache:2.4-2.3    "httpd-foreground"       22 hours ago        Up 3 hours (healthy)    80/tcp, 443/tcp                                          cvs-bob_web_1
c6c94ca6d903        docksal/cli:2.11-php7.3   "/opt/startup.sh sup…"   22 hours ago        Up 3 hours (healthy)    22/tcp, 3000/tcp, 9000/tcp                               cvs-bob_cli_1
4c0d9b5cb0fe        docksal/mysql:5.6-1.5     "docker-entrypoint.s…"   22 hours ago        Up 3 hours (healthy)    0.0.0.0:33064->3306/tcp                                  cvs-bob_db_1
85265b003733        memcached:1.4-alpine      "docker-entrypoint.s…"   22 hours ago        Up 3 hours              11211/tcp                                                cvs-bob_memcached_1
e70894054201        docksal/solr:1.0-solr4    "/opt/solr/bin/solr …"   22 hours ago        Up 3 hours              8983/tcp                                                 cvs-bob_solr_1
45ed0bf67889        docksal/ssh-agent:1.3     "docker-entrypoint.s…"   22 hours ago        Up 22 hours (healthy)                                                            docksal-ssh-agent
d41c9df08b8b        docksal/dns:1.1           "docker-entrypoint.s…"   22 hours ago        Up 22 hours (healthy)   192.168.64.100:53->53/udp                                docksal-dns
6397f5e05fad        docksal/vhost-proxy:1.6   "docker-entrypoint.s…"   22 hours ago        Up 22 hours (healthy)   192.168.64.100:80->80/tcp, 192.168.64.100:443->443/tcp   docksal-vhost-proxy
```

#### Hostnames

Hostname | Purpose
--- | ---
PROJECTNAME.local | Access to Drupal.
mail.PROJECTNAME.local | Mailhog interface that catches outbound e-mails.
solr.PROJECTNAME.local | Solr admin interface.

### CI/CD

Acquia Pipelines @todo.

### Cloud

Acquia Cloud @todo.

## Workflow

### Git

develop/release/master with feature branch per ticket @todo.

### Coding standards

### Workflow standards

## Testing/validation

### Standards

GrumPHP @todo.

### Visual regression

BackstopJS @todo.

## Command reference

### add (this project) commands

Command | Description | Examples
--- | --- | ---
sys:init | Initialize local host system. | `add sys:init`
platform:init | Create a new platform in the system. | `add platform:init`
platform:add | Add an existing platform to your environment. | `add platform:add`
sys:init | Initialize local host system. | `add sys:init`
platform:init | Create a new platform in the system. | `add platform:init`
platform:add | Add an existing platform to your environment. | `add platform:add`

### BLT commands

Command | Description | Examples
--- | --- | ---
sync:db | Sync a database between 2 Drush aliases. | `blt sync:db --source=prd --target=dev`
sync:db | Sync a database between 2 Drush aliases. | `blt sync:db --source=prd --target=dev`
sync:db | Sync a database between 2 Drush aliases. | `blt sync:db --source=prd --target=dev`
sync:db | Sync a database between 2 Drush aliases. | `blt sync:db --source=prd --target=dev`
sync:db | Sync a database between 2 Drush aliases. | `blt sync:db --source=prd --target=dev`

### Docksal commands

Command | Description | Examples
--- | --- | ---
p start | Start a set of containers for a project. | `fin p start`
p start | Start a set of containers for a project. | `fin p start`
p start | Start a set of containers for a project. | `fin p start`
p start | Start a set of containers for a project. | `fin p start`
p start | Start a set of containers for a project. | `fin p start`

### Docker commands

Sometimes you may want to use Docker directly and bypass Docksal's `fin`.

Command | Description | Examples
--- | --- | ---
ps -a | Show all containers. | `docker ps -a`
ps -a | Show all containers. | `docker ps -a`
ps -a | Show all containers. | `docker ps -a`
ps -a | Show all containers. | `docker ps -a`

### Drush commands

Command | Description | Examples
--- | --- | ---
status | Do a status check. | `drush status`
uli | Get a one time login link. | `drush uli --uri=http://example.specific.uri/`
ssh | SSH in to a remote server based on its alias. | `drush @cvshub.dev ssh`
status | Do a status check. | `drush status`
uli | Get a one time login link. | `drush uli --uri=http://example.specific.uri/`
ssh | SSH in to a remote server based on its alias. | `drush @cvshub.dev ssh`

### Other commands

Command | Description | Examples
--- | --- | ---
composer install | Install composer packages and, if available, use versions from composer.lock. | `composer install`
composer update | Attempt to update locked composer versions to newer ones within version constraints of composer.json. | `composer update`

## Summary

@todo
