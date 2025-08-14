{{-- resources/views/payment_processing.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Your Payment...</title>
    <meta http-equiv="refresh" content="5;url={{ route('order.status_check', ['order_id' => $order_id]) }}"> {{-- Fallback if JS fails --}}
</head>
<body>
    <div style="text-align: center; margin-top: 50px;">
        <h1>Processing Your Payment...</h1>
        <p>Please do not close this window.</p>
        <p>We are confirming your payment. This may take a few moments.</p>
        
    </div>

    <script>
        const orderId = "{{ $order_id }}";
        let pollInterval;
        let attempts = 0;
        const maxAttempts = 60; // Check every 5 seconds for 60 seconds (5s * 12 attempts)

        function checkPaymentStatus() {
            
            fetch(`https://waslqr.com/payment/check-status/${orderId}`) // Create a new API route for this
                .then(response => response.json())
                .then(data => {
                    
                    if (data.payement_gateway_status === 'SUCCESS') {
                        const logData = {
                                message: "SUCCESS payment status",
                                error: "SUCCESS",
                                order_id: orderId,
                                url: window.location.href,
                                timestamp: new Date().toISOString()
                            };

                            fetch('https://waslqr.com/edfapay/storeErrorLogs', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify(logData),
                            }).then(response => {
                                console.log('Error logged to backend', response);
                            }).catch(logError => {
                                console.error('Failed to log error to backend', logError);
                            });
                        clearInterval(pollInterval);
                        window.location.href = `https://waslqr.com/thank-you?order_id=${orderId}`;
                    } else if (data.payement_gateway_status === 'DECLINED') {
                                // check status again 
                                fetch(`https://waslqr.com/edfapay/checkPaymentStatus/${orderId}`) // Create a new API route for this
                                    .then(response => response.json())
                                    .then(data => { 
                                        if (data.responseBody.status === 'SUCCESS') {
                                            const logData = {
                                                    message: "SUCCESS--22 payment status",
                                                    error: "SUCCESS",
                                                    order_id: orderId,
                                                    url: window.location.href,
                                                    timestamp: new Date().toISOString()
                                                };

                                                fetch('https://waslqr.com/edfapay/storeErrorLogs', {
                                                    method: 'POST',
                                                    headers: {
                                                        'Content-Type': 'application/json',
                                                    },
                                                    body: JSON.stringify(logData),
                                                }).then(response => {
                                                    console.log('Error logged to backend', response);
                                                }).catch(logError => {
                                                    console.error('Failed to log error to backend', logError);
                                                });
                                            clearInterval(pollInterval);
                                            window.location.href = `https://waslqr.com/thank-you?order_id=${orderId}`;
                                        }
                                        else{
                                            // Log the error to your Laravel backend
                                            const logData = {
                                                message: "DECLINED --22  checking payment status",
                                                error: JSON.stringify(error),
                                                order_id: orderId,
                                                url: window.location.href,
                                                timestamp: new Date().toISOString()
                                            };

                                            fetch('https://waslqr.com/edfapay/storeErrorLogs', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                },
                                                body: JSON.stringify(logData),
                                            }).then(response => {
                                                console.log('Error logged to backend', response);
                                            }).catch(logError => {
                                                console.error('Failed to log error to backend', logError);
                                            });
                                                
                                        console.error('Error checking payment status:', error);
                                        clearInterval(pollInterval);
                                        window.location.href = `https://waslqr.com/fail?order_id=${orderId}`;
                                        }

                                    }).catch(error => {
                                        // Log the error to your Laravel backend
                                            const logData = {
                                                message: "Error checking payment status",
                                                error: JSON.stringify(error),
                                                order_id: orderId,
                                                url: window.location.href,
                                                timestamp: new Date().toISOString()
                                            };

                                            fetch('https://waslqr.com/edfapay/storeErrorLogs', {
                                                method: 'POST',
                                                headers: {
                                                    'Content-Type': 'application/json',
                                                },
                                                body: JSON.stringify(logData),
                                            }).then(response => {
                                                console.log('Error logged to backend', response);
                                            }).catch(logError => {
                                                console.error('Failed to log error to backend', logError);
                                            });
                                                
                                        console.error('Error checking payment status:', error);
                                        clearInterval(pollInterval);
                                        window.location.href = `https://waslqr.com/fail?order_id=${orderId}`;
                                    })
                                    // check status again 
                        //   const logData = {
                        //         message: " DECLINED - Error checking payment status",
                        //         error: "DECLINED",
                        //         order_id: orderId,
                        //         url: window.location.href,
                        //         timestamp: new Date().toISOString()
                        //     };

                        //     fetch('https://waslqr.com/edfapay/storeErrorLogs', {
                        //         method: 'POST',
                        //         headers: {
                        //             'Content-Type': 'application/json',
                        //         },
                        //         body: JSON.stringify(logData),
                        //     }).then(response => {
                        //         console.log('Error logged to backend', response);
                        //     }).catch(logError => {
                        //         console.error('Failed to log error to backend', logError);
                        //     });
                        // clearInterval(pollInterval);
                        // window.location.href = `https://waslqr.com/fail?order_id=${orderId}`;
                    } else {
                        attempts++;
                        if (attempts >= maxAttempts) {
                            
                            const logData = {
                                message: "maxAttempts - Error checking payment status",
                                error: "maxAttempts  timed out",
                                order_id: orderId,
                                url: window.location.href,
                                timestamp: new Date().toISOString()
                            };

                            fetch('https://waslqr.com/edfapay/storeErrorLogs', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify(logData),
                            }).then(response => {
                                console.log('Error logged to backend', response);
                            }).catch(logError => {
                                console.error('Failed to log error to backend', logError);
                            });
                            clearInterval(pollInterval);
                            // If max attempts reached and still not complete/failed, redirect to a pending page
                            window.location.href = `https://waslqr.com/fail?order_id=${orderId}`;
                        }
                    }
                })
                .catch(error => {
                    // check status again 
                    fetch(`https://waslqr.com/edfapay/checkPaymentStatus/${orderId}`) // Create a new API route for this
                        .then(response => response.json())
                        .then(data => { 
                            if (data.responseBody.status === 'SUCCESS') {
                                const logData = {
                                        message: "SUCCESS--22 payment status",
                                        error: "SUCCESS",
                                        order_id: orderId,
                                        url: window.location.href,
                                        timestamp: new Date().toISOString()
                                    };

                                    fetch('https://waslqr.com/edfapay/storeErrorLogs', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                        },
                                        body: JSON.stringify(logData),
                                    }).then(response => {
                                        console.log('Error logged to backend', response);
                                    }).catch(logError => {
                                        console.error('Failed to log error to backend', logError);
                                    });
                                clearInterval(pollInterval);
                                window.location.href = `https://waslqr.com/thank-you?order_id=${orderId}`;
                            }
                            else{
                                // Log the error to your Laravel backend
                                const logData = {
                                    message: "DECLINED --22  checking payment status",
                                    //error: JSON.stringify(error),
                                    order_id: orderId,
                                    url: window.location.href,
                                    timestamp: new Date().toISOString()
                                };

                                fetch('https://waslqr.com/edfapay/storeErrorLogs', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify(logData),
                                }).then(response => {
                                    console.log('Error logged to backend', response);
                                }).catch(logError => {
                                    console.error('Failed to log error to backend', logError);
                                });
                                    
                            console.error('Error checking payment status:', error);
                            clearInterval(pollInterval);
                            window.location.href = `https://waslqr.com?order_id=${orderId}`;
                            }

                        }).catch(error => {
                            // Log the error to your Laravel backend
                                const logData = {
                                    message: "catch Error-22 checking payment status",
                                    //error: JSON.stringify(error),
                                    order_id: orderId,
                                    url: window.location.href,
                                    timestamp: new Date().toISOString()
                                };

                                fetch('https://waslqr.com/edfapay/storeErrorLogs', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                    },
                                    body: JSON.stringify(logData),
                                }).then(response => {
                                    console.log('Error logged to backend', response);
                                }).catch(logError => {
                                    console.error('Failed to log error to backend', logError);
                                });
                                    
                            console.error('Error checking payment status:', error);
                            clearInterval(pollInterval);
                            window.location.href = `https://waslqr.com?order_id=${orderId}`;
                        })
                        // check status again 

                    
                });
        }

        // Start polling every 5 seconds
        pollInterval = setInterval(checkPaymentStatus, 1000);
        checkPaymentStatus(); // Check immediately on load
    </script>
</body>
</html>