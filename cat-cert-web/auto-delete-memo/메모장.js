const timeEnd = 20 * 1000; 
let interval = null;

function memo_save() {
    let title = document.getElementById("title").value;
    let content = document.getElementById("content").value;
    let memoTime = Date.now();

    if (title === "" || content === "") {
        alert("제목 혹은 내용이 입력되지 않았습니다.");
        return 0;
    }
    
    let memo = { //객체
        id: memoTime,
        title: title,
        content: content,
        end: memoTime + timeEnd //memoTime 추가 안하면 새로고침하면 다시 10초로 됨
    };

    let memoList = JSON.parse(sessionStorage.getItem("memoList")) || [];
    memoList.push(memo);
    sessionStorage.setItem("memoList",JSON.stringify(memoList));

    document.getElementById("title").value="";
    document.getElementById("content").value="";

    showMemo();
}

function showMemo() {
    let saving = document.getElementById("saving");
    saving.innerHTML = "";

    let memoList = JSON.parse(sessionStorage.getItem("memoList")) || [];

    for (let i=0;i<memoList.length;i++) {
        let m = memoList[i];
        saving.innerHTML += `
            <li>
                <span onclick="viewMemo(${m.id})">${m.title}</span>
                <span id="timer-${m.id}"></span>
                <input type="button" value="삭제" onclick="deleteMemo(${m.id})">
            </li>
        `;
    }

    //목록이 새로 그려질 때마다 기존 인터벌 지우고 다시 시작
    if (interval)clearInterval(interval); 
    interval = setInterval(updateTime,1000);
    updateTime();
}

function updateTime() {
    let memoList = JSON.parse(sessionStorage.getItem("memoList")) || [];
    let now = Date.now();


    for (let i=0;i<memoList.length;i++) {
        let m = memoList[i];
        let timeLeft = Math.ceil((m.end-now)/1000);

        if (timeLeft <= 0) {
        memoList = memoList.filter(memo => memo.id !== m.id);
        sessionStorage.setItem("memoList", JSON.stringify(memoList));
        showMemo();
        
    } else {
        let timer = document.getElementById(`timer-${m.id}`);
        if(timer) {
            timer.innerHTML = `(${timeLeft}초 남음)`;
        }
    }
    }

    
}

function viewMemo(id) {
    let memoList = JSON.parse(sessionStorage.getItem("memoList")) || [];
    let targetMemo = memoList.find(m => m.id === id);
    
    if (targetMemo) {
        alert(`[${targetMemo.title}]\n\n${targetMemo.content}`);
    }
}   

function deleteMemo(id) {
     let memoList = JSON.parse(sessionStorage.getItem("memoList")) || [];
     memoList = memoList.filter(m => m.id !== id);
     sessionStorage.setItem("memoList",JSON.stringify(memoList));
     showMemo();
}

showMemo();