<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <table id="modelTable">
        <tr>
            <th>Name</th>
            <th>Count</th>
            <th>Performance</th>
            <th>Queued</th>
            <th>Jobs</th>
            <th>ETA</th>
            <th>Type</th>
        </tr>
    </table>

    <div class="button-container">
        <a href="index.php" class="button">Go to Index</a>
        <a href="models.php" class="button">Go to Models</a>
    </div>

    <script>
        $(document).ready(function() {
            var modelArray = ['AbsoluteReality', 'Analog Madness', 'BRA', 'ChilloutMix', 'Edge Of Realism', 'Henmix Real', 'Hassanblend', 'HRL', 'Juggernaut XL', 'Liberty', 'majicMIX realistic', 'Neurogen', 'PFG', 'Photon', 'PPP', 'RealBiter', 'Real Dos Mix', 'Realisian', 'Realistic Vision', 'URPM', 'Zeipher Female Model'];

            // Object to store previous values for each model
            var previousValues = {};
            // Object to store previous classes for each metric of each model
            var previousClasses = {};

            function updateDataForModel(index) {
                if (index >= modelArray.length) {
                    // Reset index if it exceeds array length
                    index = 0;
                }

                var model = modelArray[index];
                console.log(model);
                $.ajax({
                    url: 'https://stablehorde.net/api/v2/status/models/' + model,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        var data = response[0]; // Access the first object in the array

                        // Create a new row for each model if it doesn't exist
                        var row = $('#modelTable tr[model="' + model + '"]');
                        if (row.length === 0) {
                            row = $('<tr model="' + model + '">').appendTo('#modelTable');
                        }

                        // Check if there are previous values for the model
                        var countClass = '';
                        var performanceClass = '';
                        var queuedClass = '';
                        var jobsClass = '';
                        var etaClass = '';

                        if (previousValues[model]) {
                            countClass = data.count > previousValues[model].count ? 'increase' : (data.count < previousValues[model].count ? 'decrease' : (previousClasses[model] && previousClasses[model].count ? previousClasses[model].count : ''));
                            performanceClass = data.performance > previousValues[model].performance ? 'increase' : (data.performance < previousValues[model].performance ? 'decrease' : (previousClasses[model] && previousClasses[model].performance ? previousClasses[model].performance : ''));
                            queuedClass = data.queued > previousValues[model].queued ? 'increase' : (data.queued < previousValues[model].queued ? 'decrease' : (previousClasses[model] && previousClasses[model].queued ? previousClasses[model].queued : ''));
                            jobsClass = data.jobs > previousValues[model].jobs ? 'increase' : (data.jobs < previousValues[model].jobs ? 'decrease' : (previousClasses[model] && previousClasses[model].jobs ? previousClasses[model].jobs : ''));
                            etaClass = data.eta > previousValues[model].eta ? 'increase' : (data.eta < previousValues[model].eta ? 'decrease' : (previousClasses[model] && previousClasses[model].eta ? previousClasses[model].eta : ''));
                        }

                        // Update table cells in the row with data from the JSON response
                        row.html('' +
                            '<td class="name">' + data.name + '</td>' +
                            '<td class="count">' + (countClass ? '<span class="' + countClass + '">' + data.count + '</span>' : data.count) + '</td>' +
                            '<td class="performance">' + (performanceClass ? '<span class="' + performanceClass + '">' + data.performance + '</span>' : data.performance) + '</td>' +
                            '<td class="queued">' + (queuedClass ? '<span class="' + queuedClass + '">' + data.queued + '</span>' : data.queued) + '</td>' +
                            '<td class="jobs">' + (jobsClass ? '<span class="' + jobsClass + '">' + data.jobs + '</span>' : data.jobs) + '</td>' +
                            '<td class="eta">' + (etaClass ? '<span class="' + etaClass + '">' + data.eta + '</span>' : data.eta) + '</td>' +
                            '<td class="type">' + data.type + '</td>'
                        );

                        // Update previous values and classes for the model
                        previousValues[model] = {
                            count: data.count,
                            performance: data.performance,
                            queued: data.queued,
                            jobs: data.jobs,
                            eta: data.eta
                        };

                        previousClasses[model] = {
                            count: countClass,
                            performance: performanceClass,
                            queued: queuedClass,
                            jobs: jobsClass,
                            eta: etaClass
                        };

                        // Call the function recursively with the next index after a delay
                        setTimeout(function() {
                            updateDataForModel(index + 1);
                        }, 120);
                    },
                    error: function(xhr, status, error) {
                        console.log('Error occurred while fetching data for model: ' + model);
                        // Call the function recursively with the next index after a delay even if an error occurs
                        setTimeout(function() {
                            updateDataForModel(index + 1);
                        }, 5000); // Retry after 5 seconds
                    }
                });
            }
            // Start updating data for models
            updateDataForModel(0);
        });
    </script>

</body>

</html>
