# thank you for taking time to go through my API, it means alot to me
# to start, edit your env file to a database name of your choice, username and password of your choice
# make sure you run artisan update, as vendor folder is not attached
# please add this constants to your env file
# WEEKDAY_PRICE = 2
# WEEKEND_PRICE = 3
# SUMMER_PRICE = 5
# WINTER_PRICE = 6

# TOTAL_PARKING= 10
# run 'php artisan migrate, to setup your database tables'
# I have setup two seeders file, run 'php artisan db:seed --class=BookingSeeder' to setup Booking.
# You can access the api routes through your postman.
# the route names are in the api file
# A trait class was also added to share features between normal booking class and admin booking class.
# helper url - http://127.0.0.1:8000/api/bookings - all bookings
# http://127.0.0.1:8000/api/booking?from_date=2023-12-15&to_date=2023-12-20 - booking within a date range
# http://127.0.0.1:8000/api/availability?from_date=2023-12-15&to_date=2023-12-20 - available booking within a date range
# I have also implemented Sanctum to create access tokens for authenticated users and admin authentication
# There should be 15 testings features.
# run 'php artisan test' for each tests, and please go through and give a feedback.
# thank you
