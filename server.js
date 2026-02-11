const express = require('express');
const path = require('path');
const nodemailer = require('nodemailer');
const app = express();
const PORT = 3000;

app.use(express.static(path.join(__dirname, 'public')));
app.use(express.json()); 
app.use(express.urlencoded({ extended: true }));


// E-pasta sūtītāja iestatījumi
const transporter = nodemailer.createTransport({
    service: 'gmail',
    auth: {
        user: 'marisblums13@gmail.com', // Tavs epasts
        pass: 'tava-lietotnes-parole'  // Google App Password
    }
});

// Lapu maršruti
app.get('/', (req, res) => res.sendFile(path.join(__dirname, 'public', 'index.html')));
app.get('/par-mums', (req, res) => res.sendFile(path.join(__dirname, 'public', 'par-mums.html')));
app.get('/pakalpojumi', (req, res) => res.sendFile(path.join(__dirname, 'public', 'pakalpojumi.html')));

// API kontaktu formai (E-pastam)
const fs = require('fs'); // Pievieno šo augšā pie pārējiem require

// ... (tavs iepriekšējais kods) ...

app.post('/api/offer', (req, res) => {
    const filePath = path.join(__dirname, 'pieteikumi.json');
    const newOffer = {
        id: Date.now(), // unikāls ID
        datums: new Date().toLocaleString('lv-LV'),
        ...req.body
    };

    fs.readFile(filePath, 'utf8', (err, data) => {
        let json = [];
        if (!err && data) {
            try {
                json = JSON.parse(data);
            } catch (parseErr) {
                json = [];
            }
        }

        json.push(newOffer);

        fs.writeFile(filePath, JSON.stringify(json, null, 2), (err) => {
            if (err) {
                console.error("Kļūda rakstot failā:", err);
                return res.status(500).json({ success: false });
            }
            console.log("✅ Jauns pieteikums saglabāts pieteikumi.json");
            res.json({ success: true });
        });
    });
});

app.use((req, res) => {
    res.status(404).send('<h1>404 - Lapa nav atrasta</h1><a href="/">Atgriezties</a>');
});

app.listen(PORT, () => {
    console.log(`🚀 Serveris griežas uz http://localhost:${PORT}`);
});