const checkboxes = document.querySelectorAll('.chk');
const kolicine = document.querySelectorAll('.kolicina');
const cene = document.querySelectorAll('.cena');
const ukupnaPolja = document.querySelectorAll('.ukupno');
const totalEl = document.getElementById('total');

checkboxes.forEach((chk, index) => {
    chk.addEventListener('change', () => {
        kolicine[index].disabled = !chk.checked;
        cene[index].disabled = !chk.checked;
        updateRow(index);
    });
});

kolicine.forEach((k, index) => {
    k.addEventListener('input', () => updateRow(index));
});
cene.forEach((c, index) => {
    c.addEventListener('input', () => updateRow(index));
});

function updateRow(index) {
    if (checkboxes[index].checked) {
        let k = parseFloat(kolicine[index].value) || 0;
        let c = parseFloat(cene[index].value) || 0;
        let ukupno = k * c;
        ukupnaPolja[index].value = ukupno.toFixed(2);
    } else {
        ukupnaPolja[index].value = 0;
    }
    updateTotal();
}

function updateTotal() {
    let total = 0;
    ukupnaPolja.forEach(u => total += parseFloat(u.value) || 0);
    totalEl.textContent = total.toFixed(2);
}

updateTotal();
