const containerInfo = document.querySelector('.content');
let currentHistory = null;


export async function loadHistory(){
    const response = await fetch('api/getHistory.php');
    const data = await response.json();
    renderHistory(data);
}

function renderHistory(history){
    const bodyHistory = document.getElementById('history-list');
    bodyHistory.innerHTML = '';
    history.forEach(element => {
        bodyHistory.insertAdjacentHTML('beforeend',`
            <div class="history-item" data-id='${element.id}'>${element.title}</div>
            `)
    });

}


async function openHistory(id){
    const bodyRight = document.getElementById('result-area');
    bodyRight.innerHTML = '';

    const response = await fetch(`api/getCurrentHistory.php?id=${id}`);
    const result = await response.json();
    currentHistory = result;
    bodyRight.insertAdjacentHTML("beforeend", `
    <div class="result-card">

        <h2 class="section-title">Исходный материал</h2>

        <div class="file-box">
            <p class="file-label">файл:</p>
        </div>

        <h2 class="section-title">Извлечённый текст (Original)</h2>
    <div class="text-box">
        <p>${result.original_text}</p>
    </div>

    <div class="translate-actions">
        <button class="translate-btn" data-lang="ru">Перевести на русский</button>
        <button class="translate-btn" data-lang="kz">Перевести на казахский</button>
        <button class="translate-btn" data-lang="en">Перевести на английский</button>
    </div>

    <h2 class="section-title">Результат перевода</h2>
    <div id="translation-output" class="translation-box">
        <p class="placeholder">Выберите язык для перевода…</p>
    </div>

</div>
`);
        let insertObj = null;
        if(result.file_type === 'image'){
            insertObj = document.createElement('img');
            insertObj.src = result.file_path;
        }else if(result.file_type === 'audio'){
            insertObj = document.createElement('audio');
            insertObj.src = result.file_path;
            insertObj.controls = true; 
        }else if(result.file_type === 'video'){
            insertObj = document.createElement('video');
            insertObj.controls = true;    
            insertObj.src = result.file_path;

        }
        document.querySelector('.file-box').append(insertObj);
}

function setLanguage(language){
    let text = document.querySelector('.placeholder');
    console.log(currentHistory);
    let q = 'translated_'+language;
    text.textContent = currentHistory[q];
}

document.addEventListener('click',(e)=>{
    let input = e.target.closest('.history-item');
    if(!input) return;
    let id = input.dataset.id;
    openHistory(id);
})

containerInfo.addEventListener('click',(e)=>{
    let btn = e.target.closest('.translate-btn');
    if(!btn) return;
    console.log(btn);
    let lang = btn.dataset.lang;
    setLanguage(lang);
})


loadHistory();