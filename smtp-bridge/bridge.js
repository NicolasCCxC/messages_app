const { SMTPServer } = require("smtp-server");
const nodemailer = require("nodemailer");

// 1. Configure your REAL credentials (The "Bridge" uses these to talk to Gmail)
const transporter = nodemailer.createTransport({
  service: "gmail",
  auth: {
    user: "nicolassanchez0115@gmail.com",
    pass: "hips tzfr jara keyn",
  },
});

// 2. Create the Mock Server (Your App talks to this)
const server = new SMTPServer({
  // Disable Authentication entirely (Like the Bank)
  authOptional: true,
  
  // Handle the incoming email
  onData(stream, session, callback) {
    let chunks = [];
    stream.on("data", (chunk) => chunks.push(chunk));
    stream.on("end", async () => {
      const message = Buffer.concat(chunks).toString();
      
      console.log("📨 Received email from Spring Boot (No Auth)...");
      
      try {
        // Parse the raw email manually is hard, so we just use the raw envelope
        // Simpler approach: We just relay the raw stream
        await transporter.sendMail({
          envelope: {
            from: session.envelope.mailFrom.address,
            to: session.envelope.rcptTo.map(t => t.address)
          },
          raw: message
        });
        console.log("✅ Forwarded to Gmail successfully!");
        callback();
      } catch (err) {
        console.error("❌ Error forwarding:", err);
        callback(new Error("Failed to relay email"));
      }
    });
  },
});

// 3. Start listening on port 2525
server.listen(2525, () => {
  console.log("🏦 Mock Bank SMTP Server running on port 2525");
  console.log("👉 Configure Spring Boot to host: localhost, port: 2525, auth: false");
});