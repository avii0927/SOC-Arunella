# 📱 Arunella - Localhost Mobile Testing Guide

Since the Arunella Smart Agricultural Supply Chain System is built as a fully responsive mobile-first web interface, you can easily load and test it on a **physical mobile phone** during your demonstrations. Follow this step-by-step guide to route your WampServer localhost page to your mobile device over your local Wi-Fi network.

---

## 🛠️ Step 1: Connect to the Same Wi-Fi Network
Ensure both your **Windows PC** (running WampServer) and your **mobile phone** are connected to the **same Wi-Fi network** (e.g., your home router or your mobile hotspot).

---

## 🛠️ Step 2: Allow External Connections in Apache (WampServer)
By default, WampServer blocks external devices from accessing your server for security reasons. You must allow access:

1. Right-click or left-click the green **WampServer icon** in your taskbar.
2. Go to **Apache** ➡️ **httpd-vhosts.conf** (Virtual Hosts configuration file). This will open the file in Notepad.
3. Look for the `<Directory>` configuration block. It usually looks like this:
   ```apache
   <VirtualHost *:80>
     ServerName localhost
     DocumentRoot "c:/wamp64/www"
     <Directory "c:/wamp64/www/">
       Options +Indexes +FollowSymLinks +MultiViews
       AllowOverride All
       Require local
     </Directory>
   </VirtualHost>
   ```
4. Change the line `Require local` to:
   ```apache
   Require all granted
   ```
5. Save the file (Ctrl + S) and close Notepad.
6. Click the WampServer tray icon and select **Restart All Services** to apply the changes. Make sure the icon turns green again.

---

## 🛠️ Step 3: Find your PC's Local IP Address
To direct your mobile phone's browser to your PC, you need your PC's IP address on the local Wi-Fi:

1. Press `Windows Key + R`, type **`cmd`**, and press Enter to open the Command Prompt.
2. Type **`ipconfig`** and press Enter.
3. Scroll and look for your active network connection adapter (usually **Wireless LAN adapter Wi-Fi** or **Ethernet adapter**).
4. Locate the **IPv4 Address**. It will look similar to this:
   👉 **`192.168.1.100`** or **`192.168.8.12`**
5. Write down this IP address.

---

## 🛠️ Step 4: Configure Windows Firewall (If Access is Blocked)
Sometimes Windows Firewall blocks incoming connections from mobile phones. If you cannot access the page in the next step, allow Port 80 through your firewall:

1. Click the Windows Start menu, search for **Windows Defender Firewall with Advanced Security**, and open it.
2. Click on **Inbound Rules** in the left sidebar.
3. Click **New Rule...** in the right sidebar.
4. Select **Port** and click Next.
5. Choose **TCP** and specify **Specific local ports**: **`80`**, then click Next.
6. Select **Allow the connection** and click Next.
7. Keep all profiles checked (Domain, Private, Public) and click Next.
8. Name the rule **`WampServer Apache Port 80`** and click Finish.

---

## 📱 Step 5: Open on your Mobile Phone
1. Open the browser (Chrome, Safari, or Firefox) on your mobile phone.
2. In the address bar, type your PC's IPv4 address followed by `/arunella/`:
   👉 **`http://<YOUR_PC_IP>/arunella/`**
   *(For example: `http://192.168.1.100/arunella/`)*
3. Press Enter.
4. **Congratulations!** Your mobile browser will load the Arunella homepage. You can now log in, register, list crops, make purchases, and assign delivery tasks directly from your phone screen.
