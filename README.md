# The Bug Genie Gitter integration module

## Installation

### 1: The Bug Genie module installation

Clone this repository either straight into a folder under `thebuggenie/modules/gitter`,
or symlink it to the same folder (IMPORTANT: The folder name under thebuggenie/modules
MUST be `gitter`, as this MUST match the module name). 

You can now enable the module from the configuration section in The Bug Genie.

## Configuration

To use the Gitter integration, you must set up a webhook in Gitter for the channel you want to post to.

Go to your Gitter channel, click "Room settings » Integrations", and select "The Bug Genie". 
Copy the webhook URL that is generated on that page.

Now, go to a project in The Bug Genie, click "Settings » Gitter integration". Paste the
webhook URL you just copied, and select which events you want to be posted.

## Reporting issues

If you find any issues, please report them in the issue tracker on our website:
http://issues.thebuggenie.com/module-slack
