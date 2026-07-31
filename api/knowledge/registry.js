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

export function getKnowledge(message){

    const text=message.toLowerCase()

    const selected=[]

    for(const module of modules){

        const score=module.keywords.filter(keyword=>

            text.includes(keyword.toLowerCase())

        ).length

        if(score>0){

            selected.push({

                priority:module.priority,

                data:module.data

            })

        }

    }

    selected.sort((a,b)=>b.priority-a.priority)

    return `
${systemPrompt}

# DATA SEKOLAH

${JSON.stringify(selected.map(item=>item.data),null,2)}
`
}