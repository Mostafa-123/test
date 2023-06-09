import pandas as pd
import mysql.connector
from sklearn.metrics.pairwise import cosine_similarity

# Establish a connection to the phpMyAdmin database
db = mysql.connector.connect(
    host="localhost",
    user="root",
    password=None,
    database="befores"
)

# Retrieve data from the tables using SQL queries
booking_data = pd.read_sql_query("SELECT * FROM bookings", db)
likes_data = pd.read_sql_query("SELECT * FROM likes", db)
comments_data = pd.read_sql_query("SELECT * FROM comments", db)
favorites_data = pd.read_sql_query("SELECT * FROM favorites", db)
halls_data = pd.read_sql_query("SELECT * FROM halls", db)

# Perform data preprocessing and create a user-item matrix
user_item_matrix = pd.merge(likes_data, booking_data, on='user_id', how='outer')
user_item_matrix = user_item_matrix.groupby(['user_id', 'hall_id']).size().unstack().fillna(0)

# Calculate user similarity using cosine similarity
user_similarity = cosine_similarity(user_item_matrix)

# Generate recommendations for a target user based on similar users' preferences
target_user_id = 1  # Example target user ID

similar_users = user_similarity[target_user_id - 1]  # Adjust for zero-based indexing
similar_users_indices = similar_users.argsort()[::-1]  # Sort in descending order

recommended_halls = []
for user_index in similar_users_indices:
    if user_index != target_user_id - 1:  # Exclude the target user
        similar_user_halls = user_item_matrix.iloc[user_index]
        recommended_halls.extend(similar_user_halls[similar_user_halls > 0].index)
        if len(recommended_halls) >= 5:  # Limit the number of recommendations
            break

# Get the hall details for the recommended halls
recommended_halls_details = halls_data[halls_data['hall_id'].isin(recommended_halls)]

# Print the recommended halls for the target user
print("Recommended Halls:")
print(recommended_halls_details[['hall_id', 'hall_name']])
