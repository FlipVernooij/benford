<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>
    <script>
        generateRandomSet = function(count){
            document.getElementById('json').value="Loading";
            /// This always fails due to Javascripts?OSX?Chrome's randomizer.
            const data = []
            for (let i = 0; i < count; i++) {
                // Generate a random number following Benford's Law
                const firstDigit = Math.floor(Math.random() * 9) + 1;
                const remainingDigits = Math.floor(Math.random() * 1000000000); // 9 digits

                // Combine the first digit and remaining digits
                const number = firstDigit * 1000000000 + remainingDigits;

                // Add the number to the data array
                data.push(number);
            }
            document.getElementById('json').value=JSON.stringify(data)
            return false;
        }

        const generateBenfordsLawData = function (count) {
            document.getElementById('json').value="Loading";
            const generateFirstDigit = function() {
                // Javascript's random function (at-least on osx/chrome) will eventually always equal the distribution of digits.
                //    As so a Javacript number comming from random will never comply to benfords law.
                // Let's hack this quickly.
                const probabilities = [0.3010, 0.1761, 0.1249, 0.0969, 0.0792, 0.0669, 0.0579, 0.0512, 0.0458];
                const rand = Math.random();
                let cumulativeProbability = 0;

                for (let i = 1; i <= 9; i++) {
                    cumulativeProbability += probabilities[i - 1];

                    if (rand <= cumulativeProbability) {
                        return i;
                    }
                }
                // This should not happen, but return 1 just in case
                return 1;
            }
            const data = [];
            for (let i = 0; i < count; i++) {
                const firstDigit = generateFirstDigit();
                const remainingDigits = Math.floor(Math.random() * 1000000000); // 9 digits
                const number = firstDigit * 1000000000 + remainingDigits;

                data.push(number);
            }
            document.getElementById('json').value=JSON.stringify(data)

        }
    </script>
    <style>
        form{width: 600px}
        textarea{width: 500px; height: 200px;}
    </style>
</head>
<body>
    <form method="post" action="/api/fraud/benford">
        <fieldset>
            <legend>Define integer set</legend>
            <dl>
                <dt>
                    import from json
                </dt>
                <dd>
                    <textarea name="test_set" id="json"></textarea>
                </dd>
                <dd>
                    <button type="button" onclick="generateRandomSet(1047591)">Generate non matching set</button>
                    <button type="button" onclick="generateBenfordsLawData(1047591)">Generate matching set</button>
                    <br />
                    <small>These buttons create about 1.000.000 records, it takes a few seconds.</small>
                </dd>
                <dt>
                    Threshold
                </dt>
                <dd>
                    <input type="number" step="0.01" min="0" name="threshold" value=".15" />
                </dd>
                <dt>
                    Submit
                </dt>
                <dd>
                    <button type="submit">Get results</button>
                </dd>
            </dl>
        </fieldset>
    </form>

</body>
</html>
