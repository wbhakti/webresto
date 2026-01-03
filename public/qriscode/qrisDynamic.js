/* ======================================================
   CRC16 CCITT-FALSE (QRIS Official Standard)
====================================================== */
function crc16qris(input) {
    let crc = 0xFFFF;

    for (let i = 0; i < input.length; i++) {
        crc ^= (input.charCodeAt(i) << 8);

        for (let j = 0; j < 8; j++) {
            if (crc & 0x8000) {
                crc = ((crc << 1) ^ 0x1021) & 0xFFFF;
            } else {
                crc = (crc << 1) & 0xFFFF;
            }
        }
    }

    return crc.toString(16).toUpperCase().padStart(4, "0");
}

/* ======================================================
   Convert QRIS Statis → QRIS Dinamis (Insert Nominal)
====================================================== */
function makeDynamicQR(qris, nominal) {

    // --- 1. Cari TAG 63 (CRC lama) dan hapus ---
    const crcTagPos = qris.lastIndexOf("6304");
    if (crcTagPos === -1) {
        throw new Error("Tag CRC '6304' tidak ditemukan");
    }

    let base = qris.substring(0, crcTagPos);

    // --- 2. Ubah static (010211) → dynamic (010212) hanya di awal string ---
    if (base.startsWith("000201010211")) {
        base = "000201010212" + base.substring("000201010211".length);
    }

    // --- 3. Buat TAG 54 (nominal) ---
    const nominalStr = nominal.toString();
    const tag54 = "54" + nominalStr.length.toString().padStart(2, "0") + nominalStr;

    // --- 4. Hapus TAG 54 lama jika ada ---
    base = base.replace(/54\d{2}\d+/g, "");

    // --- 5. Sisipkan TAG 54 sebelum TAG 58 ---
    const idx58 = base.indexOf("5802");
    if (idx58 === -1) {
        throw new Error("Tag 58 (Country Code) tidak ditemukan");
    }

    base = base.slice(0, idx58) + tag54 + base.slice(idx58);

    // --- 6. Tambahkan placeholder CRC ---
    const build = base + "6304";

    // --- 7. Hitung CRC final ---
    const crc = crc16qris(build);

    return build + crc;
}

/* ======================================================
   Export agar bisa dipakai di mana saja
====================================================== */
if (typeof module !== "undefined") {
    // Node.js / Laravel via Node process
    module.exports = { makeDynamicQR, crc16qris };
} else {
    // Browser global
    window.makeDynamicQR = makeDynamicQR;
    window.crc16qris = crc16qris;
}
