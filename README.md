# Client SSM
A client management system for Susumamas Org

## README When Setting up!!!

### How Deploy WorksHow Deploy Works
There are two instances of this app... for the livesite and testsite.
In each instance has a **deployer** file that is used by Bitbucket's Webhooks.
Everytime a push is made into the repo, the **deployer** for each instances will be accessed without relying on which branch it was pushed.

For live instance it will be named : **deployer-livesite.sh**
For testsite instance will be named : **deployer-testsite.sh**

**deployer-livesite.sh** should only reside in master branch
**deployer-testsite.sh** should only reside in testsite branch

Carefull as both these files should not be mixed in the branches.