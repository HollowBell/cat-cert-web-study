let now_input = "";
let now_cal = "";
let first_num = null;

function num_click(num) {
    if (num === "." && now_input.includes(".")) return; 
    now_input += num;
    document.getElementById("input").innerHTML += num;
}

function cal_click(cal) {
    if (now_input === "" && first_num === null) return;
    if (now_input !== "") {
        first_num = parseFloat(now_input);
    }
    
    now_cal = cal; //연산자 저장
    now_input = ""; //입력 초기화
    document.getElementById("input").innerHTML = first_num + " " + now_cal + " ";
}

function calculate() {
    if (first_num === null || now_input === "") return;

    let second_num = parseFloat(now_input);  // now_cal -> now_input
    let result;

    if (now_cal === "+") result = first_num + second_num;
    else if (now_cal === "-") result = first_num - second_num;
    else if (now_cal === "*") result = first_num * second_num;
    else if (now_cal === "/") {
        if (second_num===0) result = "0으로 나눌 수 없음";
        else result = first_num / second_num;
    }
    

    document.getElementById("output").innerHTML = result;
    document.getElementById("input").innerHTML = first_num + " " + now_cal + " " + second_num + " =";

    first_num = result;
    now_input = "";
    now_cal = "";
}

function clearAll() {
    now_input = "";
    now_cal = "";
    first_num = null;
    document.getElementById("input").innerHTML = "";
    document.getElementById("output").innerHTML = "0";
}

function clearLast() {
    now_input = now_input.slice(0,-1);
    if (first_num !== null) {
        document.getElementById("input").innerHTML = first_num + " " + now_cal + " " + now_input;
    } else {
        document.getElementById("input").innerHTML = now_input;
    }
}

function clearInput() {
    now_input = "";
    if (first_num !== null) {
        document.getElementById("input").innerHTML = first_num + " " + now_cal + " " + now_input; 
    } else {
        document.getElementById("input").innerHTML = "";
    }
    
}


function minus() {
    if (now_input === "") return;
    if (now_input[0] === "-") {
        now_input = now_input.slice(1);
    } else {
        now_input = "-" + now_input;
    }
    if (first_num !== null) {
        document.getElementById("input").innerHTML = first_num + " " + now_cal + " " + now_input;
    } else {
        document.getElementById("input").innerHTML = now_input;
    }
}