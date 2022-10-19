#   2022 Eric Margadonna
#   This is a broker that handles database access/management for login

# DBMS is MySQL, IPC service is AMQP/Rabbit
import mysql.connector, pika

# Start by establishing a connection to the DB
# cnx = mysql.connector.connect(user='chi', password='0000',
#                               host='127.0.0.1',
#                               database='IT490')

# # Establish a connection to the message queue
# connection = pika.BlockingConnection()

# # Create a channel for the connection
# channel = connection.channel()

# # Declare the queue
# channel.queue_declare(queue='login')

# # Define a callback function for the queue
# def callback(ch, method, properties, body):
#     print(" [x] Received %r" % body)

# # Start consuming messages from the queue
# channel.basic_consume('login',callback,True)

# # Start the consumer
# channel.start_consuming()

connection = pika.BlockingConnection()
channel = connection.channel()

for method_frame, properties, body in channel.consume('loginQueue'):
    # Display the message parts and acknowledge the message
    print(method_frame, properties, body)

# Cancel the consumer and return any pending messages
requeued_messages = channel.cancel()
print('Requeued %i messages' % requeued_messages)
connection.close()

# Close the connection
connection.close()

# Close the DB connection
#cnx.close()