import systemPrompt from "./systemPrompt.js"

import school from "./data/school.json" with { type:"json" }
import ppdb from "./data/ppdb.json" with { type:"json" }
import jurusan from "./data/jurusan.json" with { type:"json" }
import koperasi from "./data/koperasi.json" with { type:"json" }

const modules=[
    school,
    ppdb,
    jurusan,
    koperasi  
]

function calculateSimilarity(str1, str2){
    const s1 = str1.toLowerCase().trim()
    const s2 = str2.toLowerCase().trim()
    
    if(s1 === s2) return 1
    if(s1.includes(s2) || s2.includes(s1)) return 0.8
    
    let matches = 0
    for(let i = 0; i < Math.min(s1.length, s2.length); i++){
        if(s1[i] === s2[i]) matches++
    }
    return matches / Math.max(s1.length, s2.length)
}

function extractKeywords(text){
    return text.toLowerCase()
        .replace(/[^\w\s]/g, ' ')
        .split(/\s+/)
        .filter(word => word.length > 2)
}

function scoreModule(message, module){
    const keywords = extractKeywords(message)
    const moduleKeywords = module.keywords.map(k => k.toLowerCase())
    
    let score = 0
    
    for(const keyword of keywords){
        for(const moduleKeyword of moduleKeywords){
            const similarity = calculateSimilarity(keyword, moduleKeyword)
            if(similarity > 0.6){
                score += similarity * 10
            }
        }
    }
    
    return score
}

export function getKnowledge(message){

    const text = message.toLowerCase()
    
    const scored = modules.map(module => ({
        module: module,
        score: scoreModule(message, module)
    }))
    
    const selected = scored
        .filter(item => item.score > 0)
        .sort((a,b) => {
            const scoreDiff = b.score - a.score
            if(scoreDiff !== 0) return scoreDiff
            return b.module.priority - a.module.priority
        })
        .slice(0, 3)
    
    const knowledge = selected.length > 0 
        ? JSON.stringify(selected.map(item => ({
            module: item.module.keywords[0],
            ...item.module.data
        })), null, 2)
        : "Tidak ada data spesifik yang cocok"
    
    return `
${systemPrompt}

# DATA RELEVAN

${knowledge}

# INSTRUKSI TAMBAHAN

- Jika ada data relevan di atas, gunakan untuk menjawab
- Jika tidak ada data relevan, tetap bantu dengan pengetahuan umum
- Selalu berusaha memahami konteks pertanyaan user
- Jika user menanyakan hal yang mirip dengan keyword, cobalah berikan jawaban terbaik
`
}