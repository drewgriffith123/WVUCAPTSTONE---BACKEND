from app import app
from flaskext.mysql import MySQL

mysql = MySQL()
app.config['MYSQL_DATABASE_USER'] = 'root'
app.config['MYSQL_DATABASE_PASSWORD'] = 'o311O35!hH91d838'
app.config['MYSQL_DATABASE_DB'] = 'satData'
app.config['MYSQL_DATABASE_HOST'] = 'localhost'
mysql.init_app(app)