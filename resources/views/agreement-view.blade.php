<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')
    {{-- @include('layouts.navigation') --}}
</head>

<body>
    <div>
        <div class="flex justify-between p-8">
            <button class="p-3 bg-[#317256] rounded text-white ml-6">Upload Pdf</button>
            <button class="p-3 bg-[#317256] rounded text-white mr-6">Download</button>
        </div>
        
        <div class="w-11/12 p-4 mt-10 mr-10 border border-gray-300 rounded ml-14">
            <p class="flex justify-center mb-4">Customer Declaration</p>
            <span class="font-light">I do hereby acknowledge that I have read and understood the terms and conditions of the Automobiles Unlimited rental agreement and agree to abide by all of them.</span>
            <div class="border border-gray-100 p-14">
                <div class="flex">
                    <div class="w-1/3 mr-4">
                        <label for="customer">Customer</label>
                        <input type="text" placeholder="Customer" style="border: none; border-bottom: 1px solid #000;">
                    </div>
                    <div class="w-1/3 mr-4 ">
                        <label for="Date">Date</label>
                        <input type="date" placeholder="Date" style="border: none; border-bottom: 1px solid #000;">
                    </div>
                    <!-- First Signature Field -->
                    <div class="w-1/3">
                        <label for="Signature">Signature</label>
                        <button id="clearSignature" class="ml-44">Clear</button>
                        <canvas id="signatureCanvas" class="border border-black" width="300" height="80"></canvas>
                    </div>             
                    
                </div>
                <div class="flex mt-6">
                    <div class="w-1/3 mr-4 ">
                        <label for="Driver">Authorized Driver</label>
                        <input type="text" placeholder="Customer" style="border: none; border-bottom: 1px solid #000;">
                    </div>
                    <div class="w-1/3 mr-4 ">
                        <label for="Date">Date</label>
                        <input type="date" placeholder="Date" style="border: none; border-bottom: 1px solid #000;">
                    </div>
                    <!-- Second Signature Field -->
                    <div class="w-1/3">
                        <label for="Signature2">Signature</label>
                        <button id="clearSignature2" class="ml-44">Clear</button>
                        <canvas id="signatureCanvas2" class="border border-black" width="300" height="80"></canvas>
                    </div>
                
                    <script>
                        // JavaScript code for handling both signature canvases
                        const canvas1 = document.getElementById("signatureCanvas");
                        const ctx1 = canvas1.getContext("2d");
                        let drawing1 = false;
                
                        const canvas2 = document.getElementById("signatureCanvas2");
                        const ctx2 = canvas2.getContext("2d");
                        let drawing2 = false;
                
                        // Function to handle drawing on canvas
                        function handleDrawing(canvas, ctx, drawing) {
                            canvas.addEventListener("mousedown", () => {
                                drawing = true;
                                ctx.beginPath();
                            });
                
                            canvas.addEventListener("mousemove", (e) => {
                                if (!drawing) return;
                                ctx.lineWidth = 2;
                                ctx.lineCap = "round";
                                ctx.strokeStyle = "black";
                                ctx.lineTo(e.clientX - canvas.offsetLeft, e.clientY - canvas.offsetTop);
                                ctx.stroke();
                            });
                
                            canvas.addEventListener("mouseup", () => {
                                drawing = false;
                                ctx.closePath();
                            });
                
                            return drawing;
                        }
                
                        drawing1 = handleDrawing(canvas1, ctx1, drawing1);
                        drawing2 = handleDrawing(canvas2, ctx2, drawing2);
                
                        document.getElementById("clearSignature").addEventListener("click", () => {
                            ctx1.clearRect(0, 0, canvas1.width, canvas1.height);
                        });
                
                        document.getElementById("clearSignature2").addEventListener("click", () => {
                            ctx2.clearRect(0, 0, canvas2.width, canvas2.height);
                        });
                    </script>
            
                </div> 
            </div>
        </div>
    </div>
    

    <!-- start navigation -->
    <div class="mt-36">
        @include('layouts.footer')
    </div>
    <!-- end navigation -->

</body>

</html>

{{-- <script>
    const canvas = document.getElementById('signatureCanvas');
    const context = canvas.getContext('2d');
    const clearButton = document.getElementById('clearSignature');

    let isDrawing = false;

    canvas.addEventListener('mousedown', () => {
        isDrawing = true;
        context.beginPath();
        context.moveTo(event.clientX - canvas.getBoundingClientRect().left, event.clientY - canvas.getBoundingClientRect().top);
    });

    canvas.addEventListener('mousemove', () => {
        if (!isDrawing) return;
        context.lineTo(event.clientX - canvas.getBoundingClientRect().left, event.clientY - canvas.getBoundingClientRect().top);
        context.stroke();
    });

    canvas.addEventListener('mouseup', () => {
        isDrawing = false;
    });

    clearButton.addEventListener('click', () => {
        context.clearRect(0, 0, canvas.width, canvas.height);
    });

</script> --}}
