import pika

# Establish a connection to the message queue
connection = pika.BlockingConnection()

# Create a channel for the connection
channel = connection.channel()

channel.basic_publish(exchange='login', routing_key='*',
                      body=b'Testing!')
connection.close()