import school from "./school.js"
import jurusan from "./jurusan.js"
import ppdb from "./ppdb.js"
import faq from "./faq.js"
import kontak from "./kontak.js"
import tataTertib from "./tataTertib.js"

const modules = [
  school,
  jurusan,
  ppdb,
  faq,
  kontak,
  tataTertib
]

export function getKnowledge(question){

  const text = question.toLowerCase()

  const result = [school.content]

  for(const module of modules){

    if(module.name === "school") continue

    const match = module.keywords.some(keyword =>
      text.includes(keyword)
    )

    if(match){
      result.push(module.content)
    }

  }

  return result.join("\n\n")

}