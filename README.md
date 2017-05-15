# git-setup
Scripts to setup a Drupal Git repo and integrate it into our tools

# Usage:
0. This may be getting a lot of updates, so first ensure that you have the latest version. `git pull`
1. Navigate to your Drupal site's git root. e.g. `cd ~/Sites/my-project`
2. Run the script `/path/to/the/script/git-setup`
3. Most of the setup was done outside of the git repo (`[project-root]/.git`), but some is within the git repo.  If you are the first person on the team to run the script then you should commit the changes: `git add . && git commit -m "Initial git repo configuration."`
4. Create something great! 💃
