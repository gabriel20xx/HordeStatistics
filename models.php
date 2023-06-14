<!DOCTYPE html>
<html>
<head>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        table {
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .increase {
            color: green;
        }

        .decrease {
            color: red;
        }
    </style>
</head>

<body>
    <select id="modelSelect">
        <option value="URPM">URPM</option>
        <option value="Zack3D">Zack3D</option>
        <option value="PPP">PPP</option>
    </select>

    <p id="selectedModel"></p>

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
            <td>Name</td>
            <td id="name"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
        <tr>
            <td>Count</td>
            <td id="count"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
        <tr>
            <td>Performance</td>
            <td id="performance"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
        <tr>
            <td>Queued</td>
            <td id="queued"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
        <tr>
            <td>Jobs</td>
            <td id="jobs"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
        <tr>
            <td>ETA</td>
            <td id="eta"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
        <tr>
            <td>Type</td>
            <td id="type"></td>
            <td class="average-minute"></td>
            <td class="average-hour"></td>
            <td class="average-day"></td>
            <td class="last-changed"></td>
        </tr>
    </table>

    <script>
        $(document).ready(function() {
            var previousValues = {};
            var valueHistory = {};
            var averageIntervalMinute = 60; // Set the interval in seconds for calculating the average for the last minute
            var averageIntervalHour = 3600; // Set the interval in seconds for calculating the average for the last hour
            var averageIntervalDay = 86400; // Set the interval in seconds for calculating the average for the last day

            var modelSelect = $("#modelSelect");
            var selectedModel = $("#selectedModel");

            modelSelect.on("change", function() {
                var model = modelSelect.val();
                selectedModel.text("Selected Model: " + model);
            });

            if (!modelSelect.val()) {
                modelSelect.val(modelSelect.find("option:first").val());
                selectedModel.text("Selected Model: " + modelSelect.val());
            }

            function updateData() {
                var model = modelSelect.val();
                $.ajax({
                    url: 'https://stablehorde.net/api/v2/status/models/' + model,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var data = response[0]; // Access the first object in the array

                        var currentTime = Math.floor(Date.now() / 1000);
                        $.each(data, function(key, value) {
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
                    return valueTimestamp.timestamp >= timeThreshold && !isNaN(valueTimestamp.value);
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
