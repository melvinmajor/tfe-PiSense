# Setup for Raspberry Pi

## Raspbian - logging to RAM

- _**Creating the ramdisk:**_
  First step is to add 2 entries in the `fstab` file, like on the file stored on GitHub.
  - `tmpfs           /var/tmp        tmpfs   size=10M,nodev,nosuid     0       0
     tmpfs           /var/cache/samba tmpfs   size=5M,nodev,nosuid     0       0`
- _**The log2ram script:**_
  We need to save this script in `/usr/local/bin/log2ram` which will append and delete the contents from all log files in `/var/tmp/log/` to the files in `/var/log/`.
  - `sudo ln ~/Git/PiMium/log2ram/log2ram /usr/local/bin/log2ram`
  - Also, it needs to be executable: `sudo chmod +x /usr/local/bin/log2ram`
- _**Adding to crontab:**_
  We want to run ths script every 3 hours and add this line to the system's `/etc/crontab`.
  - `10 */3  * * *   root    /usr/local/bin/log2ram`
  Don't forget a final newline which is needed by crontab!
- _**Installing the log2disk.service:**_
  We need to create a systemd service in `/lib/systemd/system/log2ram.service` that executes this script before shutdown and reboot, so that the log file contents gets preserved and install it.
  - `sudo ln log2ram.service /lib/systemd/system/log2ram.service`
     `sudo systemctl enable log2ram`
- _**Selecting the log files for RAM:**_
  Now we can tell `/etc/rsyslog.conf` which logfiles to keep in RAM.
  These files are `auth.log`, `syslog`, `daemon.log`, `user.log` and `messages` and we replace for each of their entries the `log/` path by `tmp/log/`; for example like this: `auth,authpriv.*                 /var/tmp/log/auth.log`.
  - For that, if the file doesn't exist, simply: `sudo ln ~/Git/PiMium/log2ram/rsyslog.conf /etc/rsyslog.conf`.
  - Else, change manually the lines.

Done!

After a reboot, the system will now log the most frequent log entries to `/var/tmp/log` and sync them back every 3 hours and before shut down.

We can use `iotop` again to find a significantly reduced write activity.
However we should not be worried about the green ACT LED flashing.
Apparently this is not a good write access indicator.
