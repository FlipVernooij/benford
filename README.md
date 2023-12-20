# Simple implementation example of the Benford-law.
I took the provided [wikipedia](https://en.wikipedia.org/wiki/Benford%27s_law) page as a reference, did some research myself and came to the following solution(s)

I created a helper method in:
```
    /app/Helper/FraudDetection.php
```

With the helper I created a command:
```
sail php artisan app:is-benford "[1,2,....]" --threshold=.15 --adjust_for_big_set
```
An API GET endpoint, documenting the requirements for the POST request.
```
http://localhost/api/fraud/benford
```
An API POST endpoint, read GET response for details.
```
http://localhost/api/fraud/benford
```
And and perhaps the easiest, a simple webpage posting data to the api.

```http://localhost/```

## @todo's
I add multiple todo's mainly within the Helper method, all points of improvement.
I also created an BenfordResponse object, I might want to refactor that a little if the implementations would become more clear.
