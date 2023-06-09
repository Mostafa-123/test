import pandas as pd
from sklearn.metrics.pairwise import cosine_similarity
import json
import mysql.connector

db = mysql.connector.connect(
    host="localhost",
    user="root",
    password=None,
    database="befores"
)

booking_data = pd.read_sql_query("SELECT * FROM bookings", db)
likes_data = pd.read_sql_query("SELECT * FROM likes", db)
comments_data = pd.read_sql_query("SELECT * FROM comments", db)
favorites_data = pd.read_sql_query("SELECT * FROM favourites", db)
halls_data = pd.read_sql_query("SELECT * FROM halls", db)

user_likes = likes_data[['user_id', 'hall_id']]
user_comments = comments_data[['user_id', 'hall_id']]
user_favorites = favorites_data[['user_id', 'hall_id']]

user_preferences = pd.concat([user_likes, user_comments, user_favorites], ignore_index=True)
user_preferences = user_preferences.drop_duplicates()
user_item_matrix = user_preferences.pivot_table(index='user_id', columns='hall_id', aggfunc=lambda x: 1, fill_value=0)
user_similarity = cosine_similarity(user_item_matrix)
user_similarity_df = pd.DataFrame(user_similarity, index=user_item_matrix.index, columns=user_item_matrix.index)

def recommend_halls(user_id, top_n=5):
    user_likes = user_preferences[user_preferences['user_id'] == user_id]['hall_id']
    similar_users = user_similarity_df[user_id].sort_values(ascending=False)[1:top_n+1].index
    similar_users_likes = user_preferences[user_preferences['user_id'].isin(similar_users)]['hall_id']
    recommended_halls = similar_users_likes.value_counts().index[:top_n]
    return recommended_halls

# Replace 'user_id' with the actual user ID you want to recommend halls for
recommended_halls_json = recommend_halls(user_id=6)

# Convert the output to JSON format
recommendations_json = json.dumps({"user_id": 6, "recommended_halls": recommended_halls_json.tolist()})
print(recommendations_json)
