<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <table>
        <tr>
            <th>Attribute</th>
            <th>Value</th>
            <th>Average (last 1 minute)</th>
            <th>Average (last 1 hour)</th>
            <th>Average (last 24 hours)</th>
            <th>Last Changed</th>
        </tr>
        <tr>
            <td>Worker Count</td>
            <td id="worker_count"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
        <tr>
            <td>Queued Requests</td>
            <td id="queued_requests"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
        <tr>
            <td>Queued Text Requests</td>
            <td id="queued_text_requests"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
        <tr>
            <td>Text Worker Count</td>
            <td id="text_worker_count"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
        <tr>
            <td>Thread Count</td>
            <td id="thread_count"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
        <tr>
            <td>Text Thread Count</td>
            <td id="text_thread_count"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
        <tr>
            <td>Queued Megapixelsteps</td>
            <td id="queued_megapixelsteps"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
        <tr>
            <td>Past Minute Megapixelsteps</td>
            <td id="past_minute_megapixelsteps"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
        <tr>
            <td>Queued Forms</td>
            <td id="queued_forms"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
        <tr>
            <td>Interrogator Count</td>
            <td id="interrogator_count"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
        <tr>
            <td>Interrogator Thread Count</td>
            <td id="interrogator_thread_count"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
        <tr>
            <td>Queued Tokens</td>
            <td id="queued_tokens"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
        <tr>
            <td>Past Minute Tokens</td>
            <td id="past_minute_tokens"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
    </table>

    <div class="button-container">
        <a href="index.php" class="button">Go to Index</a>
        <a href="models.php" class="button">Go to Models</a>
    </div>

    <script>
        $(document).ready(function() {
            var previousValues = {};
            var valueHistory = {};
            var averageIntervalMinute = 60; // Set the interval in seconds for calculating the average for the last minute
            var averageIntervalHour = 3600; // Set the interval in seconds for calculating the average for the last hour
            var averageIntervalDay = 86400; // Set the interval in seconds for calculating the average for the last day

            function updateData() {
                $.ajax({
                    url: 'https://stablehorde.net/api/v2/status/performance',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var currentTime = Math.floor(Date.now() / 1000);
                        $.each(response, function(key, value) {
                            var element = $('#' + key);
                            var currentValue = value.toString();
                            var lastChangedElement = element.nextAll('.last-changed').first();
                            var averageMinuteElement = lastChangedElement.prevAll('.average-minute').first();
                            var averageHourElement = lastChangedElement.prevAll('.average-hour').first();
                            var averageDayElement = lastChangedElement.prevAll('.average-day').first();
                            var previousValue = previousValues[key] || '';

                            if (currentValue !== previousValue) {
                                element.text(currentValue);
                                lastChangedElement.data('timestamp', currentTime);
                                previousValues[key] = currentValue;

                                if (previousValue !== '') {
                                    if (parseFloat(currentValue) > parseFloat(previousValue)) {
                                        element.addClass('increase');
                                    } else if (parseFloat(currentValue) < parseFloat(previousValue)) {
                                        element.addClass('decrease');
                                    }
                                }
                            }

                            var secondsAgo = Math.round(currentTime - lastChangedElement.data('timestamp'));
                            lastChangedElement.text(secondsAgo === 0 ? 'now' : secondsAgo + ' seconds ago');

                            // Calculate average (last 1 minute)
                            if (!valueHistory[key]) {
                                valueHistory[key] = [];
                            }
                            valueHistory[key].push({
                                value: parseFloat(currentValue),
                                timestamp: currentTime
                            });

                            var averageValueMinute = calculateAverage(valueHistory[key], averageIntervalMinute);
                            averageMinuteElement.text(averageValueMinute);

                            // Calculate average (last 1 hour)
                            var averageValueHour = calculateAverage(valueHistory[key], averageIntervalHour);
                            averageHourElement.text(averageValueHour);

                            // Calculate average (last 24 hours)
                            var averageValueDay = calculateAverage(valueHistory[key], averageIntervalDay);
                            averageDayElement.text(averageValueDay);
                        });
                    },
                    error: function() {
                        console.log('Error occurred while fetching data.');
                    }
                });
            }

            // Calculate average
            function calculateAverage(values, interval) {
                var currentTime = Math.floor(Date.now() / 1000);
                var timeThreshold = currentTime - interval;

                // Filter values within the interval
                var filteredValues = values.filter(function(valueTimestamp) {
                    return valueTimestamp.timestamp >= timeThreshold;
                });

                if (filteredValues.length > 0) {
                    var sum = filteredValues.reduce(function(total, valueTimestamp) {
                        return total + valueTimestamp.value;
                    }, 0);
                    return (sum / filteredValues.length).toFixed(2);
                } else {
                    return 'N/A';
                }
            }

            updateData();
            setInterval(updateData, 1000);
        });
    </script>
</body>

</html>
