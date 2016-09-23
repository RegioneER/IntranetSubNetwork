# Regione Emilia-Romagna 's IntranetSubNetwork Piwik Plugin

This project forked the Piwik's plugin *IntranetSubNetwork* from 
<https://github.com/kwasib/IntranetSubNetwork>, 
fork of <https://github.com/pklaus/IntranetSubNetwork>, 
that was originally a fork of <http://dev.piwik.org/trac/ticket/1054>. 

This version is shipped with integrated UI customizable settings page. Piwik 2.16 is needed.

### Description

This plugin tries to identify metrics of visitors from your local network (intranet) or from any other subnet you declare.

This is done by assigning the network different names in the file
[IntranetSubNetwork.php][]
(see the [lines below #115][]).
This example lets Piwik assign the network label *Global IPv4* to 
any IPv4 visitors:

```php
if ($ip->isInRanges(array('0.0.0.0/0')))     { $networkName = 'Global IPv4'; }
```

### Installation

1. Create the folder `./IntranetSubNetwork` in the plugins folder of your Piwik installation.  
   Then copy the plugin files into that folder.
2. (optional) Adopt the networks defined in [IntranetSubNetwork.php][] to your needs.
3. Activate the plugin on Piwik's settings page.
4. Add the *Visitor Networks* widget to your Piwik Dashboard.

#### Using Git

Just execute the following commands inside the server's shell console.

```bash
cd /var/www/path/to/your/piwik/installation/plugins/
git clone git://github.com/kwasib/IntranetSubNetwork.git
```

#### Upgrade Notes

Since v0.7.0 the name of the database column which stores the network category name has changed.

Just in case you're upgrading from a previous version, run the following SQL query inside Piwik's db:

```sql
ALTER TABLE piwik_log_visit CHANGE location_IntranetSubNetwork location_subnetwork varchar(100);
```