# Render Deployment Guide

## Step-by-Step Deployment to Render

### Prerequisites
- GitHub repository with your code (✅ Done)
- Aiven MySQL database (✅ Done)
- Render account (create at render.com)

### 1. Create a New Web Service on Render

1. Go to [render.com](https://render.com) and sign up/log in
2. Click "New +" → "Web Service"
3. Connect your GitHub account if not already connected
4. Select your repository: `ogemboeugene/student-moveout-manager`
5. Click "Connect"

### 2. Configure the Web Service

**Basic Settings:**
- **Name**: `student-moveout-manager`
- **Region**: Choose closest to your users (e.g., Frankfurt for Europe)
- **Branch**: `main`
- **Runtime**: `Docker`

**Build & Deploy Settings:**
- **Dockerfile Path**: `./Dockerfile` (Render will auto-detect this)
- **Build Command**: Leave empty (Docker handles this)
- **Start Command**: Leave empty (Docker handles this)

### 3. Environment Variables

In the "Environment Variables" section, add these variables with your Aiven database details:

```
DB_HOST=your-aiven-host.aivencloud.com
DB_PORT=your-aiven-port
DB_USERNAME=your-aiven-username
DB_PASSWORD=your-aiven-password
DB_DATABASE=your-database-name
```

**To find your Aiven database details:**
1. Log into your Aiven console
2. Go to your MySQL service
3. Copy the connection details from the "Connection information" tab

### 4. Advanced Settings

- **Instance Type**: Start with "Free" (you can upgrade later)
- **Auto-Deploy**: Keep enabled (automatically deploys when you push to main branch)

### 5. Deploy

1. Click "Create Web Service"
2. Render will start building your Docker container
3. Wait for the build and deployment to complete (5-10 minutes)
4. You'll get a URL like: `https://student-moveout-manager.onrender.com`

### 6. Set Up Database Schema

Once deployed, you need to initialize your database:

1. Go to your Aiven console
2. Connect to your MySQL database using the built-in query tool or phpMyAdmin
3. Run the schema from `database/schema.sql`
4. Run the sample data from `database/sample-data.sql`

Alternatively, you can use a MySQL client:
```sql
-- Connect to your Aiven database and run:
SOURCE /path/to/schema.sql;
SOURCE /path/to/sample-data.sql;
```

### 7. Test Your Application

1. Visit your Render URL
2. Try logging in with the sample user:
   - **Username**: admin
   - **Password**: admin123

### 8. Security Considerations

**Important**: Change the default admin password immediately:
1. Log in as admin
2. Go to admin panel → Users
3. Change the admin password

**Production Checklist**:
- [ ] Change default admin password
- [ ] Update `config.php` if needed for production settings
- [ ] Consider upgrading to a paid Render plan for better performance
- [ ] Set up custom domain (optional)
- [ ] Enable HTTPS (Render provides this automatically)

### 9. Monitoring and Logs

- **Logs**: Go to your Render dashboard → Your service → Logs
- **Metrics**: Available in the Render dashboard
- **Deployments**: Track deployment history

### 10. Updating Your Application

To update your app:
1. Make changes to your code locally
2. Commit and push to GitHub
3. Render will automatically redeploy (if auto-deploy is enabled)

### Common Issues and Solutions

**Issue**: Database connection fails
**Solution**: 
- Verify environment variables are set correctly
- Check Aiven firewall settings (should allow all IPs for cloud hosting)
- Ensure your Aiven service is running

**Issue**: 404 errors for pages
**Solution**: 
- The Dockerfile includes mod_rewrite configuration
- Check that your .htaccess files are properly configured

**Issue**: Slow performance
**Solution**: 
- Upgrade from free tier to paid Render plan
- Optimize your database queries
- Consider upgrading your Aiven plan

### Cost Estimates

**Render Free Tier**:
- 750 hours/month compute time
- 500MB RAM
- Spins down after 15 minutes of inactivity
- Good for testing and development

**Render Starter Plan** (~$7/month):
- Always-on hosting
- 512MB RAM
- Better for production use

**Aiven Free Tier**:
- 1 month free trial
- Then starts at ~$9/month for smallest MySQL instance

### Support Resources

- **Render Docs**: https://render.com/docs
- **Aiven Docs**: https://aiven.io/docs
- **Your app logs**: Available in Render dashboard
