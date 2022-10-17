import mysql.connector, pika

cnx = mysql.connector.connect(user='chi', password='0000',
                              host='127.0.0.1',
                              database='IT490')

cnx.close() # close the connection
