function pad(number) {
    return number < 10 ? '0' + number : number.toString();
}

function toCRC16(input) {
    function charCodeAt(input, i) {
        return input.charCodeAt(i);
    }

    let crc = 0xFFFF;
    for (let i = 0; i < input.length; i++) {
        crc ^= charCodeAt(input, i) << 8;
        for (let j = 0; j < 8; j++) {
            crc = (crc & 0x8000) ? (crc << 1) ^ 0x1021 : crc << 1;
        }
    }

    let hex = (crc & 0xFFFF).toString(16).toUpperCase();
    return hex.length === 3 ? "0" + hex : hex;
}

function getBetween(str, start, end) {
    let startIdx = str.indexOf(start);
    if (startIdx === -1) return "";
    startIdx += start.length;
    let endIdx = str.indexOf(end, startIdx);
    return str.slice(startIdx, endIdx);
}

function dataQris(qris) {
    const nmid = "ID" + getBetween(qris, "15ID", "0303");
    const id = qris.includes("A01") ? "A01" : "01";
    const merchantName = getBetween(qris, "ID59", "60").substring(2).trim().toUpperCase();

    const printData = qris.match(/(?<=ID|COM).+?(?=0118)/g);
    const printCount = printData.length;
    const printerName = printData[printCount - 1].split('.');
    const printer = printerName.length === 3 ? printerName[1] : printerName[2];

    const nnsData = qris.match(/(?<=0118).+?(?=ID)/g);
    const nns = nnsData[nnsData.length - 1].substring(0, 8);

    const crcInput = qris.slice(0, -4);
    const crcFromQris = qris.slice(-3);
    const crcComputed = toCRC16(crcInput);

    return {
        nmid: nmid,
        id: id,
        merchantName: merchantName,
        printer: printer,
        nns: nns,
        crcIsValid: crcFromQris === crcComputed
    };
}

const makeString = (qris, { nominal, taxtype = 'p', fee = '0' } = {}) => {
    if (!qris) throw new Error('The parameter "qris" is required.');
    if (!nominal) throw new Error('The parameter "nominal" is required.');

    let tax = '';
    let qrisModified = qris.slice(0, -4).replace("010211", "010212");
    let qrisParts = qrisModified.split("5802ID");

    let amount = "54" + pad(nominal.length) + nominal;

    if (taxtype && fee) {
        tax = (taxtype === 'p')
            ? "55020357" + pad(fee.length) + fee
            : "55020256" + pad(fee.length) + fee;
    }

    amount += (tax.length === 0) ? "5802ID" : tax + "5802ID";
    let output = qrisParts[0].trim() + amount + qrisParts[1].trim();
    output += toCRC16(output);

    return output;
};


const QRCode = require('qrcode');
const Jimp = require('jimp');
const fs = require('fs');

const makeFile = async (qris, { nominal, base64 = false, taxtype = 'p', fee = '0', path = '' } = {}) => {
    try {
        const qrisModified = makeString(qris, { nominal, taxtype, fee });

        await QRCode.toFile('tmp.png', qrisModified, { margin: 2, scale: 10 });

        let data = dataQris(qris);
        let text = data.merchantName;

        const qr = await Jimp.read('tmp.png');
        const image = await Jimp.read('assets/template.png');

        const w = image.bitmap.width;
        const h = image.bitmap.height;

        const fontTitle = await Jimp.loadFont(text.length > 18 ? 'assets/font/BebasNeueSedang/BebasNeue-Regular.ttf.fnt' : 'assets/font/BebasNeue/BebasNeue-Regular.ttf.fnt');
        const fontMid = await Jimp.loadFont(text.length > 28 ? 'assets/font/RobotoSedang/Roboto-Regular.ttf.fnt' : 'assets/font/RobotoBesar/Roboto-Regular.ttf.fnt');
        const fontSmall = await Jimp.loadFont('assets/font/RobotoKecil/Roboto-Regular.ttf.fnt');

        image
            .composite(qr, w / 4 - 30, h / 4 + 68)
            .print(fontTitle, w / 5 - 30, h / 5 + 68, { text, alignmentX: Jimp.HORIZONTAL_ALIGN_CENTER, alignmentY: Jimp.VERTICAL_ALIGN_MIDDLE }, w / 1.5, text.length > 28 ? -180 : -210)
            .print(fontMid, w / 5 - 30, h / 5 + 68, { text: `NMID : ${data.nmid}`, alignmentX: Jimp.HORIZONTAL_ALIGN_CENTER, alignmentY: Jimp.VERTICAL_ALIGN_MIDDLE }, w / 1.5, text.length > 28 ? 20 : -45)
            .print(fontMid, w / 5 - 30, h / 5 + 68, { text: data.id, alignmentX: Jimp.HORIZONTAL_ALIGN_CENTER, alignmentY: Jimp.VERTICAL_ALIGN_MIDDLE }, w / 1.5, text.length > 28 ? 110 : 90)
            .print(fontSmall, w / 20, 1205, `Dicetak oleh: ${data.nns}`);

        if (!path) {
            path = `output/${text}-${Date.now()}.jpg`;
        }

        if (base64) {
            const base64Image = await image.getBase64Async(Jimp.MIME_JPEG);
            fs.unlinkSync('tmp.png');
            return base64Image;
        } else {
            await image.writeAsync(path);
            fs.unlinkSync('tmp.png');
            return path;
        }
    } catch (error) {
        throw new Error(error);
    }
};

/* ======================================================
   Export agar bisa dipakai di mana saja
====================================================== */
if (typeof module !== "undefined") {
    // Node.js / Laravel via Node process
    module.exports = { makeFile, makeString};
} else {
    // Browser global
    window.makeFile = makeFile;
    window.makeString = makeString;
}
