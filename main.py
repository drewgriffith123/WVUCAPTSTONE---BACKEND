import pymysql
from app import app
from config import mysql
from flask import jsonify
from flask import flash, request

# get all workouts and by specific ID
# get all exercises and exercises by a specific workout ID or a specific body part ID
# post a workout with exercises ID
# post a exercise

# get all athletes and athlete by specific ID
# get specific athlete workoutset 
# post athlete

# post athleteic trainer

# get TVAddWorkout

# Add a new user (subject to change if using sso)
@app.route('/CreateAthlete', methods=['POST'])
def create_emp():
    try:
        _json = request.json
        _name = _json['name']
        _email = _json['email']
        _phone = _json['phone']
        _address = _json['address']
        if _name and _email and _phone and _address and request.method == 'POST':
            conn = mysql.connect()
            cursor = conn.cursor(pymysql.cursors.DictCursor)
            sqlQuery = "INSERT INTO emp(name, email, phone, address) VALUES(%s, %s, %s, %s)"
            bindData = (_name, _email, _phone, _address)
            cursor.execute(sqlQuery, bindData)
            conn.commit()
            respone = jsonify('Employee added successfully!')
            respone.status_code = 200
            return respone
        else:
            return showMessage()
    except Exception as e:
        print(e)
    finally:
        cursor.close()
        conn.close()

# gets all athletes / gets specific athlete 
@app.route('/getAthlete/<param1>/')
def getAllSats():
    try:
        conn = mysql.connect()
        cursor = conn.cursor(pymysql.cursors.DictCursor)
        cursor.execute("SELECT * FROM satcat;")
        empRows = cursor.fetchall()
        respone = jsonify(empRows)
        respone.status_code = 200
        return respone
    except Exception as e:
        print(e)
    finally:
        cursor.close()
        conn.close()

# Assigns athlete with param1_ID a param2_ID workout set
@app.route('/assignAthlete/<param1>/<param2>')
def getAllSats():
    try:
        conn = mysql.connect()
        cursor = conn.cursor(pymysql.cursors.DictCursor)
        cursor.execute("SELECT * FROM satcat;")
        empRows = cursor.fetchall()
        respone = jsonify(empRows)
        respone.status_code = 200
        return respone
    except Exception as e:
        print(e)
    finally:
        cursor.close()
        conn.close()

@app.route('/getWorkouts/<param1>/<param2>')
def getAllSats():
    try:
        conn = mysql.connect()
        cursor = conn.cursor(pymysql.cursors.DictCursor)
        cursor.execute("SELECT * FROM satcat;")
        empRows = cursor.fetchall()
        respone = jsonify(empRows)
        respone.status_code = 200
        return respone
    except Exception as e:
        print(e)
    finally:
        cursor.close()
        conn.close()

# get all workouts / specific workout ID
@app.route('/workouts/<param1>/')
def getWorkouts():
    try:
        conn = mysql.connect()
        cursor = conn.cursor(pymysql.cursors.DictCursor)
        cursor.execute("SELECT * FROM satcat;")
        empRows = cursor.fetchall()
        respone = jsonify(empRows)
        respone.status_code = 200
        return respone
    except Exception as e:
        print(e)
    finally:
        cursor.close()
        conn.close()

# getAllworkoutcategories
@app.route('/getWorkoutCategories')
def getAllSats():
    try:
        conn = mysql.connect()
        cursor = conn.cursor(pymysql.cursors.DictCursor)
        cursor.execute("SELECT * FROM satcat;")
        empRows = cursor.fetchall()
        respone = jsonify(empRows)
        respone.status_code = 200
        return respone
    except Exception as e:
        print(e)
    finally:
        cursor.close()
        conn.close()


@app.route('/get/<param1>/<param2>')
def param(param1,param2):
    try:
        conn = mysql.connect()
        cursor = conn.cursor(pymysql.cursors.DictCursor)
        string = "SELECT "+param1+" FROM satcat"
        if param2 == "NULL":
            cursor.execute(string+";")
        else:
            cursor.execute(string+param2+";")
        rows = cursor.fetchall()
        response = jsonify(rows)
        response.status_code = 200
        return response
    except Exception as e:
        print(e)
    finally:
        cursor.close()
        conn.close()
                                                                                            
@app.errorhandler(404)
def showMessage(error=None):
    message = {
        'status': 404,
        'message': 'Record not found: ' + request.url,
    }
    respone = jsonify(message)
    respone.status_code = 404
    return respone

if __name__ == "__main__":
    from waitress import serve
    serve(app, host="0.0.0.0", port=8080)
    # app.run()

