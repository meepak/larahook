#!/bin/bash

# Start timing for the whole script
script_start_time=$(date +%s)

# Progress bar function
function progress_bar {
    local current_step=$1
    local total_steps=$2
    local message=$3

    local percent=$(( (current_step * 100) / total_steps ))
    local num_hashes=$(( (current_step * 30) / total_steps ))
    local num_spaces=$(( 30 - num_hashes ))

    echo -ne "\r[$(printf '#%.0s' $(seq 1 $num_hashes))$(printf ' %.0s' $(seq 1 $num_spaces))] $percent% - $message"
}

# Color variables
COLORS=("\033[31m" "\033[32m" "\033[33m" "\033[34m" "\033[35m" "\033[36m")
RESET_COLOR="\033[0m"
color_index=0

function color_echo {
    local message=$1
    echo -e "${COLORS[$color_index]}$message${RESET_COLOR}"
    color_index=$(( (color_index + 1) % ${#COLORS[@]} ))
}

# Steps tracking
current_step=0
total_steps=15
step_start_time=0

function start_step {
    ((current_step++))
    progress_bar $current_step $total_steps "$1"
    step_start_time=$(date +%s.%N)
}

function end_step {
    local message=$1
    local step_end_time=$(date +%s.%N)
    local step_time=$(echo "$step_end_time - $step_start_time" | bc)
    color_echo "  ✓ $message [${step_time}s]"
}

color_echo "******* Starting deployment finalization script *******"

start_step "Loading SSH agent..."
eval "$(ssh-agent -s)" >/dev/null
end_step "SSH agent running"

start_step "Adding SSH key..."
ssh-add ~/.ssh/id_ed25519 >/dev/null
end_step "SSH key loaded"

start_step "Changing working directory to project root..."
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
cd $DIR/../../ || exit
end_step "Changed directory to $(pwd)"

start_step "Cleaning up local changes..."
git stash -u >/dev/null
rm -rf public/vendor vendor
end_step "Local changes stashed, vendor directories cleaned"

start_step "Checking out origin/master..."
git checkout origin/master >/dev/null
end_step "Checked out origin/master"

start_step "Pulling origin/master..."
git pull origin main >/dev/null
end_step "Git pull completed"

start_step "Installing backend packages using Composer..."
export COMPOSER_ALLOW_SUPERUSER=1
composer update >/dev/null
end_step "Backend packages installed"

start_step "Running database migrations..."
#php artisan db:wipe --force >/dev/null
php artisan migrate --force >/dev/null
end_step "Database migrated"

start_step "Resetting backend configuration cache..."
php artisan config:clear >/dev/null
end_step "Backend configuration cache cleared"

start_step "Clearing backend caches..."
php artisan cache:clear >/dev/null
php artisan view:clear >/dev/null
php artisan route:clear >/dev/null
php artisan storage:link >/dev/null
end_step "Backend caches cleared"

start_step "Optimizing backend..."
php artisan optimize >/dev/null
end_step "Backend optimized"

start_step "Setting permissions in /var/www..."
cd .. || exit
chown -R www-data:www-data larahook/
chmod -R 655 larahook/
chmod -R +x larahook/
chmod -R 777 larahook/storage/
chmod -R 777 larahook/bootstrap/
end_step "Permissions set for all required directories"

# End timing for the whole script
script_end_time=$(date +%s)
total_time=$((script_end_time - script_start_time))
color_echo "******* Deployment finalized in ${total_time}s *******"
