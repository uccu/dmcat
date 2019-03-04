j(function () {

    let rawData = null;
    let subtitleData = null;
    let subtitleDataUpd = null;
    let newData = null;

    j('.upd1 button.a').click(function () {
        j('.upd1 input').click()
    });

    j('.upd1 button.b').click(function () {
        let files = j('.upd1 input')[0].files
        if (!files.length) {
            alert('请选择源文件'); return
        }
        let file = files[0];
        let reader = new FileReader;
        reader.readAsText(file);
        reader.onload = function () {
            try {
                window.rawData = rawData = JSON.parse(this.result);
            } catch (e) {
                alert('JSON解析失败！');
                return;
            }
            subtitleData = parseData(rawData);
            j('.upd1 span').text('解析完毕！');
        }
    });

    j('.upd2 button').click(function () {
        if (!subtitleData) {
            alert('请解析源文件'); return
        }
        parseFile(subtitleData);
        j('.upd2 span').text('下载完毕！');
    });


    j('.upd3 button.a').click(function () {
        j('.upd3 input').click()
    });
    j('.upd3 button.b').click(function () {
        let files = j('.upd3 input')[0].files
        if (!files.length) {
            alert('请选择更改后的字幕文件'); return
        }
        let file = files[0];
        let reader = new FileReader;
        reader.readAsText(file);
        reader.onload = function () {
            try {
                window.subtitleDataUpd = subtitleDataUpd = this.result;
            } catch (e) {
                alert('JSON解析失败！');
                return;
            }
            newData = complete(rawData, subtitleDataUpd);
            j('.upd3 span').text('整合完毕！');
        }
    });

    j('.upd4 button').click(function () {
        if (!newData) {
            alert('请解析源文件'); return
        }
        last(JSON.stringify(newData));
        j('.upd4 span').text('下载完毕！');
    });

})

function addZero(val, num) {
    val = parseInt(val) + '';
    let l = val.length;
    for (let i = l; i < num; i++) {
        val = '0' + val;
    }
    return val;
}

function parseData(rawData) {
    let data = [];
    let cuts = rawData.cuts;
    for (let i = 0; i < cuts.length; i++) {
        let va = '[$' + addZero(i, 6) + '$]' + (cuts[i].window ? cuts[i].window.text.trim() : '');
        data.push(va);
        console.log(va);
    }
    return data;
}

function parseFile(data1) {
    downloadFile('字幕.txt', data1.join("\r\n"));
}

function last(data1) {
    downloadFile('修改后的文件.txt', data1);
}


function downloadFile(fileName, content) {
    let aLink = document.createElement('a');
    let blob = new Blob([content]);
    let evt = document.createEvent("HTMLEvents");
    evt.initEvent("click", false, false);
    aLink.download = fileName;
    aLink.href = URL.createObjectURL(blob);
    aLink.dispatchEvent(evt);
    aLink.click();
}


function complete(rawData, subtitleDataUpd) {

    let m = subtitleDataUpd.match(/\[\$\d{6}\$\].*/ig);
    subtitleDataUpd = [];

    m.map(function (v) {
        let num = parseInt(v.slice(2, 8));
        let val = v.slice(10);
        subtitleDataUpd[num] = val;
    })

    let cuts = rawData.cuts;
    for (let i = 0; i < cuts.length; i++) {
        if (cuts[i].window) rawData.cuts[i].window.text = subtitleDataUpd[i] ? subtitleDataUpd[i] : '';
    }
    return rawData;

}
