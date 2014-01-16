# Piwik IntranetSubNetwork Plugin

This is a fork of the Piwik plugin *IntranetSubNetwork* from
<https://github.com/pklaus/IntranetSubNetwork/>, originally forked from 
<http://dev.piwik.org/trac/ticket/1054>. This version updates the plugin
for Piwik 2.x compatibility.


### Description

This plugin identifies the number of visitors from your local network (Intranet) 
or from any other subnet.  

This is done by assigning the network different names in the file
[IntranetSubNetwork.php][]
(see the [lines below #115][]).
This example lets Piwik assign the network label *Global IPv4* to 
any IPv4 visitors:

```php
<?php
if (IP::isIpInRange($visitorInfo['location_ip'], array('0.0.0.0/0')))     { $networkName = 'Global IPv4'; }
?>
````

### General Installation Instructions

1. Create the folder `./IntranetSubNetwork` in the plugins folder of your Piwik installation.  
   Then copy the plugin files into that folder.
2. (optional) Adopt the networks defined in [IntranetSubNetwork.php][] to your needs.
3. Activate the plugin on Piwik's settings page.
4. Add the *Visitor Networks* widget to your Piwik Dashboard.

#### Installation via Git

```bash
cd /var/www/path/to/your/piwik/installation/plugins/
git clone git://github.com/kwasib/IntranetSubNetwork.git
```
