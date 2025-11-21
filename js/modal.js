import { loadHistory } from "./history.js";


const modal = document.getElementById("modal");
const newChatBtn = document.getElementById("newChatBtn");
const modalCloseBtn = document.getElementById("modalCloseBtn");
const modalProcessBtn = document.getElementById("modalProcessBtn");
const modalFileInput = document.getElementById("modalFileInput");


newChatBtn.onclick = () => {
    modal.style.display = "flex";
};

modalCloseBtn.onclick = () => {
    modal.style.display = "none";
};

modalProcessBtn.onclick = async () => {
    const file = modalFileInput.files[0];
    if (!file) return alert("Выберите файл!");

    const form = new FormData();
    form.append("file", file);

    const res = await fetch("api/uploadFile.php", {
        method: "POST",
        body: form
    });

    const data = await res.json();
    console.log(data);
    modal.style.display = "none";
    await loadHistory();
};
