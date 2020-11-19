#!/bin/bash

bin_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
me=$(whoami)

# Step 1: Set up sys dir structure.
mkdir -p "${HOME}/sys/project" &&
mkdir -p "${HOME}/sys/platform" &&
mkdir -p "${HOME}/sys/app" &&
mkdir -p "${HOME}/sys/tmp" &&
mkdir -p "${HOME}/sys/etc" &&
mkdir -p "${HOME}/sys/backup" &&
mkdir -p "${HOME}/sys/bin" &&
mkdir -p "${HOME}/sys/lib" &&
mkdir -p "${HOME}/sys/etc"

if ! [ -x "$(command -v composer)" ]; then
  curl -sS https://getcomposer.org/installer | php
  mv composer.phar /usr/local/bin/composer
  chmod 755 /usr/local/bin/composer
fi

# Clone this project to ~/sys/project/devops.

# Run composer install.

# Add global symlink to add command.
path_add_symlink="/usr/local/bin/add"
path_add_symlink_target="${HOME}/sys/project/devops/vendor/bin/blt"
if [ -L ${my_link} ] ; then
  sudo ln -s "${path_add_symlink_target}" "${path_add_symlink}"
fi

# add Run sys:init
