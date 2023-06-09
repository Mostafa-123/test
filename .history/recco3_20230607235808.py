import sys
import json
import nltk
import pymysql
import pandas as pd
from nltk.corpus import stopwords
from nltk.tokenize import word_tokenize
from nltk.stem.wordnet import WordNetLemmatizer
from ast import literal_eval

def recommend_halls( description):
    # Connect to the database
    connection = pymysql.connect(
        host='localhost',
        user='root',
        password=None,
        database='befores'
    )

    cursor = connection.cursor()
    query = "SELECT * FROM halls"
    cursor.execute(query)
    result = cursor.fetchall()
    data = pd.DataFrame(result, columns=[column[0] for column in cursor.description])
    cursor.close()
    connection.close()

    # Drop unnecessary columns
    data.drop(['start_party', 'end_party', 'created_at', 'updated_at', 'owner_id', 'verified'], 1, inplace=True)

    # Lowercase the columns for simplicity
    data['address'] = data['address'].str.lower()
    data['description'] = data['description'].str.lower()

    def impute(column):
        column = column[0]
        if (type(column) != list):
            return "".join(literal_eval(column))
        else:
            return column

    # Tokenize and filter description
    description = description.lower()
    word_tokenize(description)
    stop_words = stopwords.words('english')
    lemm = WordNetLemmatizer()
    filtered = {word for word in description if not word in stop_words}
    filtered_set = set()
    for fs in filtered:
        filtered_set.add(lemm.lemmatize(fs))

    # country = data[data['address'] == location.lower()]
    country = country.set_index(pd.Index(range(country.shape[0])))
    cos = []
    for i in range(country.shape[0]):
        temp_token = word_tokenize(country["description"][i])
        temp_set = [word for word in temp_token if not word in stop_words]
        temp2_set = set()
        for s in temp_set:
            temp2_set.add(lemm.lemmatize(s))
        vector = temp2_set.intersection(filtered_set)
        cos.append(len(vector))
    country['similarity'] = cos
    country = country.sort_values(by='similarity', ascending=False)
    country.drop_duplicates(subset='name', keep='first', inplace=True)
    country.reset_index(inplace=True)

    # Calculate the suitability score for each hall based on various criteria
    country['score'] = country['rooms'] * 0.5 + country['capacity'] * 0.3 + country['tables'] * 0.2

    # Sort the halls by the suitability score
    country = country.sort_values(by='score', ascending=False)

    # Prepare the recommended halls
    recommended_halls = country[["name", "address"]].head().to_dict(orient='records')

    return recommended_halls

# Read location and description from command line arguments
location = sys.argv[1]
description = sys.argv[2]

# Call the recommendation function and get the recommended halls
recommended_halls = recommend_halls( description)

# Convert the recommended halls to JSON
recommended_halls_json = json.dumps(recommended_halls)

# Print the JSON to the console (output to be captured by PHP script)
print(recommended_halls_json)
