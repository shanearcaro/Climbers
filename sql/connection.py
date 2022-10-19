#   2022 Eric Margadonna
#   This is a broker that handles database access/management for login

# DBMS is MySQL, IPC service is AMQP/Rabbit
import mysql.connector, pika

#   Start by establishing a connection to the DB
cnx = mysql.connector.connect(user='chi', password='0000',
                              host='127.0.0.1',
                              database='IT490')

cnx.close() # close the connection
