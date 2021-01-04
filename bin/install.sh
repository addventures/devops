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

if ! [ -x "$(command -v brew)" ]; then
  /bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"
fi

if ! [ -x "$(command -v composer)" ]; then
  brew install composer
fi

# On OSX, running git without git installed prompts the xcode install.
git

if ! [ -x "$(command -v fin)" ]; then
  brew install virtualbox --cask
  bash <(curl -fsSL https://get.docksal.io)
fi

# Clone this project to ~/sys/project/devops.
devops_git_url="https://github.com/addventures/devops.git"
devops_path="${HOME}/sys/project/devops"
git clone "${devops_git_url}" "${devops_path}"

# Run composer install.
cd "${devops_path}"
composer install

# Add global symlink to add command.
path_add_symlink="/usr/local/bin/add"
path_add_symlink_target="${devops_path}/vendor/bin/blt"
if [ -L ${my_link} ] ; then
  sudo ln -s "${path_add_symlink_target}" "${path_add_symlink}"
fi

# Run sys:init for first time set up with prompts.
add sys:init
