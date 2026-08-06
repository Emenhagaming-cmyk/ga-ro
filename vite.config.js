import { defineConfig, loadEnv } from "vite"
import vue from "@vitejs/plugin-vue"
import fs from "node:fs"
import path from "path"

export default defineConfig(({ mode }) => {
  const env = loadEnv(mode, process.cwd(), "")
  if (env.GROQ_API_KEY) process.env.GROQ_API_KEY = env.GROQ_API_KEY

  // ponytail: baca src/server/.env (file key punya user) tanpa dependency dotenv
  const serverEnv = path.resolve(__dirname, "src/server/.env")
  if (fs.existsSync(serverEnv)) {
    const m = fs.readFileSync(serverEnv, "utf8").match(/^GROQ_API_KEY\s*=\s*(.+)$/m)
    if (m) process.env.GROQ_API_KEY ||= m[1].trim().replace(/^["']|["']$/g, "")
  }

  return {
    plugins: [vue(), chatApiPlugin()],

    server: {
      port: 5174,
      strictPort: true,
    },

    resolve: {
      alias: {
        "@": path.resolve(__dirname, "./src")
      }
    }
  }
})

function chatApiPlugin() {
  return {
    name: "chat-api",
    configureServer(server) {
      server.middlewares.use("/api/chat", async (req, res, next) => {
        if (req.method !== "POST") return next()

        let body = ""
        for await (const chunk of req) body += chunk

        let parsed
        try {
          parsed = JSON.parse(body || "{}")
        } catch {
          res.statusCode = 400
          res.setHeader("Content-Type", "application/json")
          return res.end(JSON.stringify({ reply: "Bad JSON body" }))
        }
        req.body = parsed

        res.status = (code) => {
          res.statusCode = code
          return res
        }
        res.json = (data) => {
          res.setHeader("Content-Type", "application/json")
          res.end(JSON.stringify(data))
        }

        const mod = await import("./api/chat.js")
        mod.default(req, res)
      })
    }
  }
}