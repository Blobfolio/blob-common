//<?php
/**
 * Blobfolio: Domain Suffixes
 *
 * Make Domain Validation Great Again.
 *
 * @see {blobfolio\common\cast}
 * @see {blobfolio\common\ref\cast}
 *
 * @package Blobfolio/Common
 * @author Blobfolio, LLC <hello@blobfolio.com>
 */

namespace Blobfolio;

use \Throwable;

final class Domains {
	private static suffixes = ["ac": ["com": [], "edu": [], "gov": [], "net": [], "mil": [], "org": []], "ad": ["nom": []], "ae": ["co": [], "net": [], "org": [], "sch": [], "ac": [], "gov": [], "mil": []], "aero": ["accident-investigation": [], "accident-prevention": [], "aerobatic": [], "aeroclub": [], "aerodrome": [], "agents": [], "aircraft": [], "airline": [], "airport": [], "air-surveillance": [], "airtraffic": [], "air-traffic-control": [], "ambulance": [], "amusement": [], "association": [], "author": [], "ballooning": [], "broker": [], "caa": [], "cargo": [], "catering": [], "certification": [], "championship": [], "charter": [], "civilaviation": [], "club": [], "conference": [], "consultant": [], "consulting": [], "control": [], "council": [], "crew": [], "design": [], "dgca": [], "educator": [], "emergency": [], "engine": [], "engineer": [], "entertainment": [], "equipment": [], "exchange": [], "express": [], "federation": [], "flight": [], "freight": [], "fuel": [], "gliding": [], "government": [], "groundhandling": [], "group": [], "hanggliding": [], "homebuilt": [], "insurance": [], "journal": [], "journalist": [], "leasing": [], "logistics": [], "magazine": [], "maintenance": [], "media": [], "microlight": [], "modelling": [], "navigation": [], "parachuting": [], "paragliding": [], "passenger-association": [], "pilot": [], "press": [], "production": [], "recreation": [], "repbody": [], "res": [], "research": [], "rotorcraft": [], "safety": [], "scientist": [], "services": [], "show": [], "skydiving": [], "software": [], "student": [], "trader": [], "trading": [], "trainer": [], "union": [], "workinggroup": [], "works": []], "af": ["gov": [], "com": [], "org": [], "net": [], "edu": []], "ag": ["com": [], "org": [], "net": [], "co": [], "nom": []], "ai": ["off": [], "com": [], "net": [], "org": []], "al": ["com": [], "edu": [], "gov": [], "mil": [], "net": [], "org": []], "am": [], "ao": ["ed": [], "gv": [], "og": [], "co": [], "pb": [], "it": []], "aq": [], "ar": ["com": [], "edu": [], "gob": [], "gov": [], "int": [], "mil": [], "musica": [], "net": [], "org": [], "tur": []], "arpa": ["e164": [], "in-addr": [], "ip6": [], "iris": [], "uri": [], "urn": []], "as": ["gov": []], "asia": [], "at": ["ac": [], "co": [], "gv": [], "or": []], "au": ["com": [], "net": [], "org": [], "edu": ["act": [], "nsw": [], "nt": [], "qld": [], "sa": [], "tas": [], "vic": [], "wa": []], "gov": ["qld": [], "sa": [], "tas": [], "vic": [], "wa": []], "asn": [], "id": [], "info": [], "conf": [], "oz": [], "act": [], "nsw": [], "nt": [], "qld": [], "sa": [], "tas": [], "vic": [], "wa": []], "aw": ["com": []], "ax": [], "az": ["com": [], "net": [], "int": [], "gov": [], "org": [], "edu": [], "info": [], "pp": [], "mil": [], "name": [], "pro": [], "biz": []], "ba": ["com": [], "edu": [], "gov": [], "mil": [], "net": [], "org": []], "bb": ["biz": [], "co": [], "com": [], "edu": [], "gov": [], "info": [], "net": [], "org": [], "store": [], "tv": []], "bd": ["*": []], "be": ["ac": []], "bf": ["gov": []], "bg": ["a": [], "b": [], "c": [], "d": [], "e": [], "f": [], "g": [], "h": [], "i": [], "j": [], "k": [], "l": [], "m": [], "n": [], "o": [], "p": [], "q": [], "r": [], "s": [], "t": [], "u": [], "v": [], "w": [], "x": [], "y": [], "z": [], "0": [], "1": [], "2": [], "3": [], "4": [], "5": [], "6": [], "7": [], "8": [], "9": []], "bh": ["com": [], "edu": [], "net": [], "org": [], "gov": []], "bi": ["co": [], "com": [], "edu": [], "or": [], "org": []], "biz": [], "bj": ["asso": [], "barreau": [], "gouv": []], "bm": ["com": [], "edu": [], "gov": [], "net": [], "org": []], "bn": ["*": []], "bo": ["com": [], "edu": [], "gob": [], "int": [], "org": [], "net": [], "mil": [], "tv": [], "web": [], "academia": [], "agro": [], "arte": [], "blog": [], "bolivia": [], "ciencia": [], "cooperativa": [], "democracia": [], "deporte": [], "ecologia": [], "economia": [], "empresa": [], "indigena": [], "industria": [], "info": [], "medicina": [], "movimiento": [], "musica": [], "natural": [], "nombre": [], "noticias": [], "patria": [], "politica": [], "profesional": [], "plurinacional": [], "pueblo": [], "revista": [], "salud": [], "tecnologia": [], "tksat": [], "transporte": [], "wiki": []], "br": ["9guacu": [], "abc": [], "adm": [], "adv": [], "agr": [], "aju": [], "am": [], "anani": [], "aparecida": [], "arq": [], "art": [], "ato": [], "b": [], "barueri": [], "belem": [], "bhz": [], "bio": [], "blog": [], "bmd": [], "boavista": [], "bsb": [], "campinagrande": [], "campinas": [], "caxias": [], "cim": [], "cng": [], "cnt": [], "com": [], "contagem": [], "coop": [], "cri": [], "cuiaba": [], "curitiba": [], "def": [], "ecn": [], "eco": [], "edu": [], "emp": [], "eng": [], "esp": [], "etc": [], "eti": [], "far": [], "feira": [], "flog": [], "floripa": [], "fm": [], "fnd": [], "fortal": [], "fot": [], "foz": [], "fst": [], "g12": [], "ggf": [], "goiania": [], "gov": ["ac": [], "al": [], "am": [], "ap": [], "ba": [], "ce": [], "df": [], "es": [], "go": [], "ma": [], "mg": [], "ms": [], "mt": [], "pa": [], "pb": [], "pe": [], "pi": [], "pr": [], "rj": [], "rn": [], "ro": [], "rr": [], "rs": [], "sc": [], "se": [], "sp": [], "to": []], "gru": [], "imb": [], "ind": [], "inf": [], "jab": [], "jampa": [], "jdf": [], "joinville": [], "jor": [], "jus": [], "leg": [], "lel": [], "londrina": [], "macapa": [], "maceio": [], "manaus": [], "maringa": [], "mat": [], "med": [], "mil": [], "morena": [], "mp": [], "mus": [], "natal": [], "net": [], "niteroi": [], "nom": ["*": []], "not": [], "ntr": [], "odo": [], "org": [], "osasco": [], "palmas": [], "poa": [], "ppg": [], "pro": [], "psc": [], "psi": [], "pvh": [], "qsl": [], "radio": [], "rec": [], "recife": [], "ribeirao": [], "rio": [], "riobranco": [], "riopreto": [], "salvador": [], "sampa": [], "santamaria": [], "santoandre": [], "saobernardo": [], "saogonca": [], "sjc": [], "slg": [], "slz": [], "sorocaba": [], "srv": [], "taxi": [], "teo": [], "the": [], "tmp": [], "trd": [], "tur": [], "tv": [], "udi": [], "vet": [], "vix": [], "vlog": [], "wiki": [], "zlg": []], "bs": ["com": [], "net": [], "org": [], "edu": [], "gov": []], "bt": ["com": [], "edu": [], "gov": [], "net": [], "org": []], "bv": [], "bw": ["co": [], "org": []], "by": ["gov": [], "mil": [], "com": [], "of": []], "bz": ["com": [], "net": [], "org": [], "edu": [], "gov": []], "ca": ["ab": [], "bc": [], "mb": [], "nb": [], "nf": [], "nl": [], "ns": [], "nt": [], "nu": [], "on": [], "pe": [], "qc": [], "sk": [], "yk": [], "gc": []], "cat": [], "cc": [], "cd": ["gov": []], "cf": [], "cg": [], "ch": [], "ci": ["org": [], "or": [], "com": [], "co": [], "edu": [], "ed": [], "ac": [], "net": [], "go": [], "asso": [], "xn--aroport-bya": [], "int": [], "presse": [], "md": [], "gouv": []], "ck": ["*": [], "www": ["!": []]], "cl": ["gov": [], "gob": [], "co": [], "mil": []], "cm": ["co": [], "com": [], "gov": [], "net": []], "cn": ["ac": [], "com": [], "edu": [], "gov": [], "net": [], "org": [], "mil": [], "xn--55qx5d": [], "xn--io0a7i": [], "xn--od0alg": [], "ah": [], "bj": [], "cq": [], "fj": [], "gd": [], "gs": [], "gz": [], "gx": [], "ha": [], "hb": [], "he": [], "hi": [], "hl": [], "hn": [], "jl": [], "js": [], "jx": [], "ln": [], "nm": [], "nx": [], "qh": [], "sc": [], "sd": [], "sh": [], "sn": [], "sx": [], "tj": [], "xj": [], "xz": [], "yn": [], "zj": [], "hk": [], "mo": [], "tw": []], "co": ["arts": [], "com": [], "edu": [], "firm": [], "gov": [], "info": [], "int": [], "mil": [], "net": [], "nom": [], "org": [], "rec": [], "web": []], "com": [], "coop": [], "cr": ["ac": [], "co": [], "ed": [], "fi": [], "go": [], "or": [], "sa": []], "cu": ["com": [], "edu": [], "org": [], "net": [], "gov": [], "inf": []], "cv": [], "cw": ["com": [], "edu": [], "net": [], "org": []], "cx": ["gov": []], "cy": ["ac": [], "biz": [], "com": [], "ekloges": [], "gov": [], "ltd": [], "name": [], "net": [], "org": [], "parliament": [], "press": [], "pro": [], "tm": []], "cz": [], "de": [], "dj": [], "dk": [], "dm": ["com": [], "net": [], "org": [], "edu": [], "gov": []], "do": ["art": [], "com": [], "edu": [], "gob": [], "gov": [], "mil": [], "net": [], "org": [], "sld": [], "web": []], "dz": ["com": [], "org": [], "net": [], "gov": [], "edu": [], "asso": [], "pol": [], "art": []], "ec": ["com": [], "info": [], "net": [], "fin": [], "k12": [], "med": [], "pro": [], "org": [], "edu": [], "gov": [], "gob": [], "mil": []], "edu": [], "ee": ["edu": [], "gov": [], "riik": [], "lib": [], "med": [], "com": [], "pri": [], "aip": [], "org": [], "fie": []], "eg": ["com": [], "edu": [], "eun": [], "gov": [], "mil": [], "name": [], "net": [], "org": [], "sci": []], "er": ["*": []], "es": ["com": [], "nom": [], "org": [], "gob": [], "edu": []], "et": ["com": [], "gov": [], "org": [], "edu": [], "biz": [], "name": [], "info": [], "net": []], "eu": [], "fi": ["aland": []], "fj": ["*": []], "fk": ["*": []], "fm": [], "fo": [], "fr": ["com": [], "asso": [], "nom": [], "prd": [], "presse": [], "tm": [], "aeroport": [], "assedic": [], "avocat": [], "avoues": [], "cci": [], "chambagri": [], "chirurgiens-dentistes": [], "experts-comptables": [], "geometre-expert": [], "gouv": [], "greta": [], "huissier-justice": [], "medecin": [], "notaires": [], "pharmacien": [], "port": [], "veterinaire": []], "ga": [], "gb": [], "gd": [], "ge": ["com": [], "edu": [], "gov": [], "org": [], "mil": [], "net": [], "pvt": []], "gf": [], "gg": ["co": [], "net": [], "org": []], "gh": ["com": [], "edu": [], "gov": [], "org": [], "mil": []], "gi": ["com": [], "ltd": [], "gov": [], "mod": [], "edu": [], "org": []], "gl": ["co": [], "com": [], "edu": [], "net": [], "org": []], "gm": [], "gn": ["ac": [], "com": [], "edu": [], "gov": [], "org": [], "net": []], "gov": [], "gp": ["com": [], "net": [], "mobi": [], "edu": [], "org": [], "asso": []], "gq": [], "gr": ["com": [], "edu": [], "net": [], "org": [], "gov": []], "gs": [], "gt": ["com": [], "edu": [], "gob": [], "ind": [], "mil": [], "net": [], "org": []], "gu": ["com": [], "edu": [], "gov": [], "guam": [], "info": [], "net": [], "org": [], "web": []], "gw": [], "gy": ["co": [], "com": [], "edu": [], "gov": [], "net": [], "org": []], "hk": ["com": [], "edu": [], "gov": [], "idv": [], "net": [], "org": [], "xn--55qx5d": [], "xn--wcvs22d": [], "xn--lcvr32d": [], "xn--mxtq1m": [], "xn--gmqw5a": [], "xn--ciqpn": [], "xn--gmq050i": [], "xn--zf0avx": [], "xn--io0a7i": [], "xn--mk0axi": [], "xn--od0alg": [], "xn--od0aq3b": [], "xn--tn0ag": [], "xn--uc0atv": [], "xn--uc0ay4a": []], "hm": [], "hn": ["com": [], "edu": [], "org": [], "net": [], "mil": [], "gob": []], "hr": ["iz": [], "from": [], "name": [], "com": []], "ht": ["com": [], "shop": [], "firm": [], "info": [], "adult": [], "net": [], "pro": [], "org": [], "med": [], "art": [], "coop": [], "pol": [], "asso": [], "edu": [], "rel": [], "gouv": [], "perso": []], "hu": ["co": [], "info": [], "org": [], "priv": [], "sport": [], "tm": [], "2000": [], "agrar": [], "bolt": [], "casino": [], "city": [], "erotica": [], "erotika": [], "film": [], "forum": [], "games": [], "hotel": [], "ingatlan": [], "jogasz": [], "konyvelo": [], "lakas": [], "media": [], "news": [], "reklam": [], "sex": [], "shop": [], "suli": [], "szex": [], "tozsde": [], "utazas": [], "video": []], "id": ["ac": [], "biz": [], "co": [], "desa": [], "go": [], "mil": [], "my": [], "net": [], "or": [], "sch": [], "web": []], "ie": ["gov": []], "il": ["ac": [], "co": [], "gov": [], "idf": [], "k12": [], "muni": [], "net": [], "org": []], "im": ["ac": [], "co": ["ltd": [], "plc": []], "com": [], "net": [], "org": [], "tt": [], "tv": []], "in": ["co": [], "firm": [], "net": [], "org": [], "gen": [], "ind": [], "nic": [], "ac": [], "edu": [], "res": [], "gov": [], "mil": []], "info": [], "int": ["eu": []], "io": ["com": []], "iq": ["gov": [], "edu": [], "mil": [], "com": [], "org": [], "net": []], "ir": ["ac": [], "co": [], "gov": [], "id": [], "net": [], "org": [], "sch": [], "xn--mgba3a4f16a": [], "xn--mgba3a4fra": []], "is": ["net": [], "com": [], "edu": [], "gov": [], "org": [], "int": []], "it": ["gov": [], "edu": [], "abr": [], "abruzzo": [], "aosta-valley": [], "aostavalley": [], "bas": [], "basilicata": [], "cal": [], "calabria": [], "cam": [], "campania": [], "emilia-romagna": [], "emiliaromagna": [], "emr": [], "friuli-v-giulia": [], "friuli-ve-giulia": [], "friuli-vegiulia": [], "friuli-venezia-giulia": [], "friuli-veneziagiulia": [], "friuli-vgiulia": [], "friuliv-giulia": [], "friulive-giulia": [], "friulivegiulia": [], "friulivenezia-giulia": [], "friuliveneziagiulia": [], "friulivgiulia": [], "fvg": [], "laz": [], "lazio": [], "lig": [], "liguria": [], "lom": [], "lombardia": [], "lombardy": [], "lucania": [], "mar": [], "marche": [], "mol": [], "molise": [], "piedmont": [], "piemonte": [], "pmn": [], "pug": [], "puglia": [], "sar": [], "sardegna": [], "sardinia": [], "sic": [], "sicilia": [], "sicily": [], "taa": [], "tos": [], "toscana": [], "trentin-sud-tirol": [], "xn--trentin-sd-tirol-rzb": [], "trentin-sudtirol": [], "xn--trentin-sdtirol-7vb": [], "trentin-sued-tirol": [], "trentin-suedtirol": [], "trentino-a-adige": [], "trentino-aadige": [], "trentino-alto-adige": [], "trentino-altoadige": [], "trentino-s-tirol": [], "trentino-stirol": [], "trentino-sud-tirol": [], "xn--trentino-sd-tirol-c3b": [], "trentino-sudtirol": [], "xn--trentino-sdtirol-szb": [], "trentino-sued-tirol": [], "trentino-suedtirol": [], "trentino": [], "trentinoa-adige": [], "trentinoaadige": [], "trentinoalto-adige": [], "trentinoaltoadige": [], "trentinos-tirol": [], "trentinostirol": [], "trentinosud-tirol": [], "xn--trentinosd-tirol-rzb": [], "trentinosudtirol": [], "xn--trentinosdtirol-7vb": [], "trentinosued-tirol": [], "trentinosuedtirol": [], "trentinsud-tirol": [], "xn--trentinsd-tirol-6vb": [], "trentinsudtirol": [], "xn--trentinsdtirol-nsb": [], "trentinsued-tirol": [], "trentinsuedtirol": [], "tuscany": [], "umb": [], "umbria": [], "val-d-aosta": [], "val-daosta": [], "vald-aosta": [], "valdaosta": [], "valle-aosta": [], "valle-d-aosta": [], "valle-daosta": [], "valleaosta": [], "valled-aosta": [], "valledaosta": [], "vallee-aoste": [], "xn--valle-aoste-ebb": [], "vallee-d-aoste": [], "xn--valle-d-aoste-ehb": [], "valleeaoste": [], "xn--valleaoste-e7a": [], "valleedaoste": [], "xn--valledaoste-ebb": [], "vao": [], "vda": [], "ven": [], "veneto": [], "ag": [], "agrigento": [], "al": [], "alessandria": [], "alto-adige": [], "altoadige": [], "an": [], "ancona": [], "andria-barletta-trani": [], "andria-trani-barletta": [], "andriabarlettatrani": [], "andriatranibarletta": [], "ao": [], "aosta": [], "aoste": [], "ap": [], "aq": [], "aquila": [], "ar": [], "arezzo": [], "ascoli-piceno": [], "ascolipiceno": [], "asti": [], "at": [], "av": [], "avellino": [], "ba": [], "balsan-sudtirol": [], "xn--balsan-sdtirol-nsb": [], "balsan-suedtirol": [], "balsan": [], "bari": [], "barletta-trani-andria": [], "barlettatraniandria": [], "belluno": [], "benevento": [], "bergamo": [], "bg": [], "bi": [], "biella": [], "bl": [], "bn": [], "bo": [], "bologna": [], "bolzano-altoadige": [], "bolzano": [], "bozen-sudtirol": [], "xn--bozen-sdtirol-2ob": [], "bozen-suedtirol": [], "bozen": [], "br": [], "brescia": [], "brindisi": [], "bs": [], "bt": [], "bulsan-sudtirol": [], "xn--bulsan-sdtirol-nsb": [], "bulsan-suedtirol": [], "bulsan": [], "bz": [], "ca": [], "cagliari": [], "caltanissetta": [], "campidano-medio": [], "campidanomedio": [], "campobasso": [], "carbonia-iglesias": [], "carboniaiglesias": [], "carrara-massa": [], "carraramassa": [], "caserta": [], "catania": [], "catanzaro": [], "cb": [], "ce": [], "cesena-forli": [], "xn--cesena-forl-mcb": [], "cesenaforli": [], "xn--cesenaforl-i8a": [], "ch": [], "chieti": [], "ci": [], "cl": [], "cn": [], "co": [], "como": [], "cosenza": [], "cr": [], "cremona": [], "crotone": [], "cs": [], "ct": [], "cuneo": [], "cz": [], "dell-ogliastra": [], "dellogliastra": [], "en": [], "enna": [], "fc": [], "fe": [], "fermo": [], "ferrara": [], "fg": [], "fi": [], "firenze": [], "florence": [], "fm": [], "foggia": [], "forli-cesena": [], "xn--forl-cesena-fcb": [], "forlicesena": [], "xn--forlcesena-c8a": [], "fr": [], "frosinone": [], "ge": [], "genoa": [], "genova": [], "go": [], "gorizia": [], "gr": [], "grosseto": [], "iglesias-carbonia": [], "iglesiascarbonia": [], "im": [], "imperia": [], "is": [], "isernia": [], "kr": [], "la-spezia": [], "laquila": [], "laspezia": [], "latina": [], "lc": [], "le": [], "lecce": [], "lecco": [], "li": [], "livorno": [], "lo": [], "lodi": [], "lt": [], "lu": [], "lucca": [], "macerata": [], "mantova": [], "massa-carrara": [], "massacarrara": [], "matera": [], "mb": [], "mc": [], "me": [], "medio-campidano": [], "mediocampidano": [], "messina": [], "mi": [], "milan": [], "milano": [], "mn": [], "mo": [], "modena": [], "monza-brianza": [], "monza-e-della-brianza": [], "monza": [], "monzabrianza": [], "monzaebrianza": [], "monzaedellabrianza": [], "ms": [], "mt": [], "na": [], "naples": [], "napoli": [], "no": [], "novara": [], "nu": [], "nuoro": [], "og": [], "ogliastra": [], "olbia-tempio": [], "olbiatempio": [], "or": [], "oristano": [], "ot": [], "pa": [], "padova": [], "padua": [], "palermo": [], "parma": [], "pavia": [], "pc": [], "pd": [], "pe": [], "perugia": [], "pesaro-urbino": [], "pesarourbino": [], "pescara": [], "pg": [], "pi": [], "piacenza": [], "pisa": [], "pistoia": [], "pn": [], "po": [], "pordenone": [], "potenza": [], "pr": [], "prato": [], "pt": [], "pu": [], "pv": [], "pz": [], "ra": [], "ragusa": [], "ravenna": [], "rc": [], "re": [], "reggio-calabria": [], "reggio-emilia": [], "reggiocalabria": [], "reggioemilia": [], "rg": [], "ri": [], "rieti": [], "rimini": [], "rm": [], "rn": [], "ro": [], "roma": [], "rome": [], "rovigo": [], "sa": [], "salerno": [], "sassari": [], "savona": [], "si": [], "siena": [], "siracusa": [], "so": [], "sondrio": [], "sp": [], "sr": [], "ss": [], "suedtirol": [], "xn--sdtirol-n2a": [], "sv": [], "ta": [], "taranto": [], "te": [], "tempio-olbia": [], "tempioolbia": [], "teramo": [], "terni": [], "tn": [], "to": [], "torino": [], "tp": [], "tr": [], "trani-andria-barletta": [], "trani-barletta-andria": [], "traniandriabarletta": [], "tranibarlettaandria": [], "trapani": [], "trento": [], "treviso": [], "trieste": [], "ts": [], "turin": [], "tv": [], "ud": [], "udine": [], "urbino-pesaro": [], "urbinopesaro": [], "va": [], "varese": [], "vb": [], "vc": [], "ve": [], "venezia": [], "venice": [], "verbania": [], "vercelli": [], "verona": [], "vi": [], "vibo-valentia": [], "vibovalentia": [], "vicenza": [], "viterbo": [], "vr": [], "vs": [], "vt": [], "vv": []], "je": ["co": [], "net": [], "org": []], "jm": ["*": []], "jo": ["com": [], "org": [], "net": [], "edu": [], "sch": [], "gov": [], "mil": [], "name": []], "jobs": [], "jp": ["ac": [], "ad": [], "co": [], "ed": [], "go": [], "gr": [], "lg": [], "ne": [], "or": [], "aichi": ["aisai": [], "ama": [], "anjo": [], "asuke": [], "chiryu": [], "chita": [], "fuso": [], "gamagori": [], "handa": [], "hazu": [], "hekinan": [], "higashiura": [], "ichinomiya": [], "inazawa": [], "inuyama": [], "isshiki": [], "iwakura": [], "kanie": [], "kariya": [], "kasugai": [], "kira": [], "kiyosu": [], "komaki": [], "konan": [], "kota": [], "mihama": [], "miyoshi": [], "nishio": [], "nisshin": [], "obu": [], "oguchi": [], "oharu": [], "okazaki": [], "owariasahi": [], "seto": [], "shikatsu": [], "shinshiro": [], "shitara": [], "tahara": [], "takahama": [], "tobishima": [], "toei": [], "togo": [], "tokai": [], "tokoname": [], "toyoake": [], "toyohashi": [], "toyokawa": [], "toyone": [], "toyota": [], "tsushima": [], "yatomi": []], "akita": ["akita": [], "daisen": [], "fujisato": [], "gojome": [], "hachirogata": [], "happou": [], "higashinaruse": [], "honjo": [], "honjyo": [], "ikawa": [], "kamikoani": [], "kamioka": [], "katagami": [], "kazuno": [], "kitaakita": [], "kosaka": [], "kyowa": [], "misato": [], "mitane": [], "moriyoshi": [], "nikaho": [], "noshiro": [], "odate": [], "oga": [], "ogata": [], "semboku": [], "yokote": [], "yurihonjo": []], "aomori": ["aomori": [], "gonohe": [], "hachinohe": [], "hashikami": [], "hiranai": [], "hirosaki": [], "itayanagi": [], "kuroishi": [], "misawa": [], "mutsu": [], "nakadomari": [], "noheji": [], "oirase": [], "owani": [], "rokunohe": [], "sannohe": [], "shichinohe": [], "shingo": [], "takko": [], "towada": [], "tsugaru": [], "tsuruta": []], "chiba": ["abiko": [], "asahi": [], "chonan": [], "chosei": [], "choshi": [], "chuo": [], "funabashi": [], "futtsu": [], "hanamigawa": [], "ichihara": [], "ichikawa": [], "ichinomiya": [], "inzai": [], "isumi": [], "kamagaya": [], "kamogawa": [], "kashiwa": [], "katori": [], "katsuura": [], "kimitsu": [], "kisarazu": [], "kozaki": [], "kujukuri": [], "kyonan": [], "matsudo": [], "midori": [], "mihama": [], "minamiboso": [], "mobara": [], "mutsuzawa": [], "nagara": [], "nagareyama": [], "narashino": [], "narita": [], "noda": [], "oamishirasato": [], "omigawa": [], "onjuku": [], "otaki": [], "sakae": [], "sakura": [], "shimofusa": [], "shirako": [], "shiroi": [], "shisui": [], "sodegaura": [], "sosa": [], "tako": [], "tateyama": [], "togane": [], "tohnosho": [], "tomisato": [], "urayasu": [], "yachimata": [], "yachiyo": [], "yokaichiba": [], "yokoshibahikari": [], "yotsukaido": []], "ehime": ["ainan": [], "honai": [], "ikata": [], "imabari": [], "iyo": [], "kamijima": [], "kihoku": [], "kumakogen": [], "masaki": [], "matsuno": [], "matsuyama": [], "namikata": [], "niihama": [], "ozu": [], "saijo": [], "seiyo": [], "shikokuchuo": [], "tobe": [], "toon": [], "uchiko": [], "uwajima": [], "yawatahama": []], "fukui": ["echizen": [], "eiheiji": [], "fukui": [], "ikeda": [], "katsuyama": [], "mihama": [], "minamiechizen": [], "obama": [], "ohi": [], "ono": [], "sabae": [], "sakai": [], "takahama": [], "tsuruga": [], "wakasa": []], "fukuoka": ["ashiya": [], "buzen": [], "chikugo": [], "chikuho": [], "chikujo": [], "chikushino": [], "chikuzen": [], "chuo": [], "dazaifu": [], "fukuchi": [], "hakata": [], "higashi": [], "hirokawa": [], "hisayama": [], "iizuka": [], "inatsuki": [], "kaho": [], "kasuga": [], "kasuya": [], "kawara": [], "keisen": [], "koga": [], "kurate": [], "kurogi": [], "kurume": [], "minami": [], "miyako": [], "miyama": [], "miyawaka": [], "mizumaki": [], "munakata": [], "nakagawa": [], "nakama": [], "nishi": [], "nogata": [], "ogori": [], "okagaki": [], "okawa": [], "oki": [], "omuta": [], "onga": [], "onojo": [], "oto": [], "saigawa": [], "sasaguri": [], "shingu": [], "shinyoshitomi": [], "shonai": [], "soeda": [], "sue": [], "tachiarai": [], "tagawa": [], "takata": [], "toho": [], "toyotsu": [], "tsuiki": [], "ukiha": [], "umi": [], "usui": [], "yamada": [], "yame": [], "yanagawa": [], "yukuhashi": []], "fukushima": ["aizubange": [], "aizumisato": [], "aizuwakamatsu": [], "asakawa": [], "bandai": [], "date": [], "fukushima": [], "furudono": [], "futaba": [], "hanawa": [], "higashi": [], "hirata": [], "hirono": [], "iitate": [], "inawashiro": [], "ishikawa": [], "iwaki": [], "izumizaki": [], "kagamiishi": [], "kaneyama": [], "kawamata": [], "kitakata": [], "kitashiobara": [], "koori": [], "koriyama": [], "kunimi": [], "miharu": [], "mishima": [], "namie": [], "nango": [], "nishiaizu": [], "nishigo": [], "okuma": [], "omotego": [], "ono": [], "otama": [], "samegawa": [], "shimogo": [], "shirakawa": [], "showa": [], "soma": [], "sukagawa": [], "taishin": [], "tamakawa": [], "tanagura": [], "tenei": [], "yabuki": [], "yamato": [], "yamatsuri": [], "yanaizu": [], "yugawa": []], "gifu": ["anpachi": [], "ena": [], "gifu": [], "ginan": [], "godo": [], "gujo": [], "hashima": [], "hichiso": [], "hida": [], "higashishirakawa": [], "ibigawa": [], "ikeda": [], "kakamigahara": [], "kani": [], "kasahara": [], "kasamatsu": [], "kawaue": [], "kitagata": [], "mino": [], "minokamo": [], "mitake": [], "mizunami": [], "motosu": [], "nakatsugawa": [], "ogaki": [], "sakahogi": [], "seki": [], "sekigahara": [], "shirakawa": [], "tajimi": [], "takayama": [], "tarui": [], "toki": [], "tomika": [], "wanouchi": [], "yamagata": [], "yaotsu": [], "yoro": []], "gunma": ["annaka": [], "chiyoda": [], "fujioka": [], "higashiagatsuma": [], "isesaki": [], "itakura": [], "kanna": [], "kanra": [], "katashina": [], "kawaba": [], "kiryu": [], "kusatsu": [], "maebashi": [], "meiwa": [], "midori": [], "minakami": [], "naganohara": [], "nakanojo": [], "nanmoku": [], "numata": [], "oizumi": [], "ora": [], "ota": [], "shibukawa": [], "shimonita": [], "shinto": [], "showa": [], "takasaki": [], "takayama": [], "tamamura": [], "tatebayashi": [], "tomioka": [], "tsukiyono": [], "tsumagoi": [], "ueno": [], "yoshioka": []], "hiroshima": ["asaminami": [], "daiwa": [], "etajima": [], "fuchu": [], "fukuyama": [], "hatsukaichi": [], "higashihiroshima": [], "hongo": [], "jinsekikogen": [], "kaita": [], "kui": [], "kumano": [], "kure": [], "mihara": [], "miyoshi": [], "naka": [], "onomichi": [], "osakikamijima": [], "otake": [], "saka": [], "sera": [], "seranishi": [], "shinichi": [], "shobara": [], "takehara": []], "hokkaido": ["abashiri": [], "abira": [], "aibetsu": [], "akabira": [], "akkeshi": [], "asahikawa": [], "ashibetsu": [], "ashoro": [], "assabu": [], "atsuma": [], "bibai": [], "biei": [], "bifuka": [], "bihoro": [], "biratori": [], "chippubetsu": [], "chitose": [], "date": [], "ebetsu": [], "embetsu": [], "eniwa": [], "erimo": [], "esan": [], "esashi": [], "fukagawa": [], "fukushima": [], "furano": [], "furubira": [], "haboro": [], "hakodate": [], "hamatonbetsu": [], "hidaka": [], "higashikagura": [], "higashikawa": [], "hiroo": [], "hokuryu": [], "hokuto": [], "honbetsu": [], "horokanai": [], "horonobe": [], "ikeda": [], "imakane": [], "ishikari": [], "iwamizawa": [], "iwanai": [], "kamifurano": [], "kamikawa": [], "kamishihoro": [], "kamisunagawa": [], "kamoenai": [], "kayabe": [], "kembuchi": [], "kikonai": [], "kimobetsu": [], "kitahiroshima": [], "kitami": [], "kiyosato": [], "koshimizu": [], "kunneppu": [], "kuriyama": [], "kuromatsunai": [], "kushiro": [], "kutchan": [], "kyowa": [], "mashike": [], "matsumae": [], "mikasa": [], "minamifurano": [], "mombetsu": [], "moseushi": [], "mukawa": [], "muroran": [], "naie": [], "nakagawa": [], "nakasatsunai": [], "nakatombetsu": [], "nanae": [], "nanporo": [], "nayoro": [], "nemuro": [], "niikappu": [], "niki": [], "nishiokoppe": [], "noboribetsu": [], "numata": [], "obihiro": [], "obira": [], "oketo": [], "okoppe": [], "otaru": [], "otobe": [], "otofuke": [], "otoineppu": [], "oumu": [], "ozora": [], "pippu": [], "rankoshi": [], "rebun": [], "rikubetsu": [], "rishiri": [], "rishirifuji": [], "saroma": [], "sarufutsu": [], "shakotan": [], "shari": [], "shibecha": [], "shibetsu": [], "shikabe": [], "shikaoi": [], "shimamaki": [], "shimizu": [], "shimokawa": [], "shinshinotsu": [], "shintoku": [], "shiranuka": [], "shiraoi": [], "shiriuchi": [], "sobetsu": [], "sunagawa": [], "taiki": [], "takasu": [], "takikawa": [], "takinoue": [], "teshikaga": [], "tobetsu": [], "tohma": [], "tomakomai": [], "tomari": [], "toya": [], "toyako": [], "toyotomi": [], "toyoura": [], "tsubetsu": [], "tsukigata": [], "urakawa": [], "urausu": [], "uryu": [], "utashinai": [], "wakkanai": [], "wassamu": [], "yakumo": [], "yoichi": []], "hyogo": ["aioi": [], "akashi": [], "ako": [], "amagasaki": [], "aogaki": [], "asago": [], "ashiya": [], "awaji": [], "fukusaki": [], "goshiki": [], "harima": [], "himeji": [], "ichikawa": [], "inagawa": [], "itami": [], "kakogawa": [], "kamigori": [], "kamikawa": [], "kasai": [], "kasuga": [], "kawanishi": [], "miki": [], "minamiawaji": [], "nishinomiya": [], "nishiwaki": [], "ono": [], "sanda": [], "sannan": [], "sasayama": [], "sayo": [], "shingu": [], "shinonsen": [], "shiso": [], "sumoto": [], "taishi": [], "taka": [], "takarazuka": [], "takasago": [], "takino": [], "tamba": [], "tatsuno": [], "toyooka": [], "yabu": [], "yashiro": [], "yoka": [], "yokawa": []], "ibaraki": ["ami": [], "asahi": [], "bando": [], "chikusei": [], "daigo": [], "fujishiro": [], "hitachi": [], "hitachinaka": [], "hitachiomiya": [], "hitachiota": [], "ibaraki": [], "ina": [], "inashiki": [], "itako": [], "iwama": [], "joso": [], "kamisu": [], "kasama": [], "kashima": [], "kasumigaura": [], "koga": [], "miho": [], "mito": [], "moriya": [], "naka": [], "namegata": [], "oarai": [], "ogawa": [], "omitama": [], "ryugasaki": [], "sakai": [], "sakuragawa": [], "shimodate": [], "shimotsuma": [], "shirosato": [], "sowa": [], "suifu": [], "takahagi": [], "tamatsukuri": [], "tokai": [], "tomobe": [], "tone": [], "toride": [], "tsuchiura": [], "tsukuba": [], "uchihara": [], "ushiku": [], "yachiyo": [], "yamagata": [], "yawara": [], "yuki": []], "ishikawa": ["anamizu": [], "hakui": [], "hakusan": [], "kaga": [], "kahoku": [], "kanazawa": [], "kawakita": [], "komatsu": [], "nakanoto": [], "nanao": [], "nomi": [], "nonoichi": [], "noto": [], "shika": [], "suzu": [], "tsubata": [], "tsurugi": [], "uchinada": [], "wajima": []], "iwate": ["fudai": [], "fujisawa": [], "hanamaki": [], "hiraizumi": [], "hirono": [], "ichinohe": [], "ichinoseki": [], "iwaizumi": [], "iwate": [], "joboji": [], "kamaishi": [], "kanegasaki": [], "karumai": [], "kawai": [], "kitakami": [], "kuji": [], "kunohe": [], "kuzumaki": [], "miyako": [], "mizusawa": [], "morioka": [], "ninohe": [], "noda": [], "ofunato": [], "oshu": [], "otsuchi": [], "rikuzentakata": [], "shiwa": [], "shizukuishi": [], "sumita": [], "tanohata": [], "tono": [], "yahaba": [], "yamada": []], "kagawa": ["ayagawa": [], "higashikagawa": [], "kanonji": [], "kotohira": [], "manno": [], "marugame": [], "mitoyo": [], "naoshima": [], "sanuki": [], "tadotsu": [], "takamatsu": [], "tonosho": [], "uchinomi": [], "utazu": [], "zentsuji": []], "kagoshima": ["akune": [], "amami": [], "hioki": [], "isa": [], "isen": [], "izumi": [], "kagoshima": [], "kanoya": [], "kawanabe": [], "kinko": [], "kouyama": [], "makurazaki": [], "matsumoto": [], "minamitane": [], "nakatane": [], "nishinoomote": [], "satsumasendai": [], "soo": [], "tarumizu": [], "yusui": []], "kanagawa": ["aikawa": [], "atsugi": [], "ayase": [], "chigasaki": [], "ebina": [], "fujisawa": [], "hadano": [], "hakone": [], "hiratsuka": [], "isehara": [], "kaisei": [], "kamakura": [], "kiyokawa": [], "matsuda": [], "minamiashigara": [], "miura": [], "nakai": [], "ninomiya": [], "odawara": [], "oi": [], "oiso": [], "sagamihara": [], "samukawa": [], "tsukui": [], "yamakita": [], "yamato": [], "yokosuka": [], "yugawara": [], "zama": [], "zushi": []], "kochi": ["aki": [], "geisei": [], "hidaka": [], "higashitsuno": [], "ino": [], "kagami": [], "kami": [], "kitagawa": [], "kochi": [], "mihara": [], "motoyama": [], "muroto": [], "nahari": [], "nakamura": [], "nankoku": [], "nishitosa": [], "niyodogawa": [], "ochi": [], "okawa": [], "otoyo": [], "otsuki": [], "sakawa": [], "sukumo": [], "susaki": [], "tosa": [], "tosashimizu": [], "toyo": [], "tsuno": [], "umaji": [], "yasuda": [], "yusuhara": []], "kumamoto": ["amakusa": [], "arao": [], "aso": [], "choyo": [], "gyokuto": [], "kamiamakusa": [], "kikuchi": [], "kumamoto": [], "mashiki": [], "mifune": [], "minamata": [], "minamioguni": [], "nagasu": [], "nishihara": [], "oguni": [], "ozu": [], "sumoto": [], "takamori": [], "uki": [], "uto": [], "yamaga": [], "yamato": [], "yatsushiro": []], "kyoto": ["ayabe": [], "fukuchiyama": [], "higashiyama": [], "ide": [], "ine": [], "joyo": [], "kameoka": [], "kamo": [], "kita": [], "kizu": [], "kumiyama": [], "kyotamba": [], "kyotanabe": [], "kyotango": [], "maizuru": [], "minami": [], "minamiyamashiro": [], "miyazu": [], "muko": [], "nagaokakyo": [], "nakagyo": [], "nantan": [], "oyamazaki": [], "sakyo": [], "seika": [], "tanabe": [], "uji": [], "ujitawara": [], "wazuka": [], "yamashina": [], "yawata": []], "mie": ["asahi": [], "inabe": [], "ise": [], "kameyama": [], "kawagoe": [], "kiho": [], "kisosaki": [], "kiwa": [], "komono": [], "kumano": [], "kuwana": [], "matsusaka": [], "meiwa": [], "mihama": [], "minamiise": [], "misugi": [], "miyama": [], "nabari": [], "shima": [], "suzuka": [], "tado": [], "taiki": [], "taki": [], "tamaki": [], "toba": [], "tsu": [], "udono": [], "ureshino": [], "watarai": [], "yokkaichi": []], "miyagi": ["furukawa": [], "higashimatsushima": [], "ishinomaki": [], "iwanuma": [], "kakuda": [], "kami": [], "kawasaki": [], "marumori": [], "matsushima": [], "minamisanriku": [], "misato": [], "murata": [], "natori": [], "ogawara": [], "ohira": [], "onagawa": [], "osaki": [], "rifu": [], "semine": [], "shibata": [], "shichikashuku": [], "shikama": [], "shiogama": [], "shiroishi": [], "tagajo": [], "taiwa": [], "tome": [], "tomiya": [], "wakuya": [], "watari": [], "yamamoto": [], "zao": []], "miyazaki": ["aya": [], "ebino": [], "gokase": [], "hyuga": [], "kadogawa": [], "kawaminami": [], "kijo": [], "kitagawa": [], "kitakata": [], "kitaura": [], "kobayashi": [], "kunitomi": [], "kushima": [], "mimata": [], "miyakonojo": [], "miyazaki": [], "morotsuka": [], "nichinan": [], "nishimera": [], "nobeoka": [], "saito": [], "shiiba": [], "shintomi": [], "takaharu": [], "takanabe": [], "takazaki": [], "tsuno": []], "nagano": ["achi": [], "agematsu": [], "anan": [], "aoki": [], "asahi": [], "azumino": [], "chikuhoku": [], "chikuma": [], "chino": [], "fujimi": [], "hakuba": [], "hara": [], "hiraya": [], "iida": [], "iijima": [], "iiyama": [], "iizuna": [], "ikeda": [], "ikusaka": [], "ina": [], "karuizawa": [], "kawakami": [], "kiso": [], "kisofukushima": [], "kitaaiki": [], "komagane": [], "komoro": [], "matsukawa": [], "matsumoto": [], "miasa": [], "minamiaiki": [], "minamimaki": [], "minamiminowa": [], "minowa": [], "miyada": [], "miyota": [], "mochizuki": [], "nagano": [], "nagawa": [], "nagiso": [], "nakagawa": [], "nakano": [], "nozawaonsen": [], "obuse": [], "ogawa": [], "okaya": [], "omachi": [], "omi": [], "ookuwa": [], "ooshika": [], "otaki": [], "otari": [], "sakae": [], "sakaki": [], "saku": [], "sakuho": [], "shimosuwa": [], "shinanomachi": [], "shiojiri": [], "suwa": [], "suzaka": [], "takagi": [], "takamori": [], "takayama": [], "tateshina": [], "tatsuno": [], "togakushi": [], "togura": [], "tomi": [], "ueda": [], "wada": [], "yamagata": [], "yamanouchi": [], "yasaka": [], "yasuoka": []], "nagasaki": ["chijiwa": [], "futsu": [], "goto": [], "hasami": [], "hirado": [], "iki": [], "isahaya": [], "kawatana": [], "kuchinotsu": [], "matsuura": [], "nagasaki": [], "obama": [], "omura": [], "oseto": [], "saikai": [], "sasebo": [], "seihi": [], "shimabara": [], "shinkamigoto": [], "togitsu": [], "tsushima": [], "unzen": []], "nara": ["ando": [], "gose": [], "heguri": [], "higashiyoshino": [], "ikaruga": [], "ikoma": [], "kamikitayama": [], "kanmaki": [], "kashiba": [], "kashihara": [], "katsuragi": [], "kawai": [], "kawakami": [], "kawanishi": [], "koryo": [], "kurotaki": [], "mitsue": [], "miyake": [], "nara": [], "nosegawa": [], "oji": [], "ouda": [], "oyodo": [], "sakurai": [], "sango": [], "shimoichi": [], "shimokitayama": [], "shinjo": [], "soni": [], "takatori": [], "tawaramoto": [], "tenkawa": [], "tenri": [], "uda": [], "yamatokoriyama": [], "yamatotakada": [], "yamazoe": [], "yoshino": []], "niigata": ["aga": [], "agano": [], "gosen": [], "itoigawa": [], "izumozaki": [], "joetsu": [], "kamo": [], "kariwa": [], "kashiwazaki": [], "minamiuonuma": [], "mitsuke": [], "muika": [], "murakami": [], "myoko": [], "nagaoka": [], "niigata": [], "ojiya": [], "omi": [], "sado": [], "sanjo": [], "seiro": [], "seirou": [], "sekikawa": [], "shibata": [], "tagami": [], "tainai": [], "tochio": [], "tokamachi": [], "tsubame": [], "tsunan": [], "uonuma": [], "yahiko": [], "yoita": [], "yuzawa": []], "oita": ["beppu": [], "bungoono": [], "bungotakada": [], "hasama": [], "hiji": [], "himeshima": [], "hita": [], "kamitsue": [], "kokonoe": [], "kuju": [], "kunisaki": [], "kusu": [], "oita": [], "saiki": [], "taketa": [], "tsukumi": [], "usa": [], "usuki": [], "yufu": []], "okayama": ["akaiwa": [], "asakuchi": [], "bizen": [], "hayashima": [], "ibara": [], "kagamino": [], "kasaoka": [], "kibichuo": [], "kumenan": [], "kurashiki": [], "maniwa": [], "misaki": [], "nagi": [], "niimi": [], "nishiawakura": [], "okayama": [], "satosho": [], "setouchi": [], "shinjo": [], "shoo": [], "soja": [], "takahashi": [], "tamano": [], "tsuyama": [], "wake": [], "yakage": []], "okinawa": ["aguni": [], "ginowan": [], "ginoza": [], "gushikami": [], "haebaru": [], "higashi": [], "hirara": [], "iheya": [], "ishigaki": [], "ishikawa": [], "itoman": [], "izena": [], "kadena": [], "kin": [], "kitadaito": [], "kitanakagusuku": [], "kumejima": [], "kunigami": [], "minamidaito": [], "motobu": [], "nago": [], "naha": [], "nakagusuku": [], "nakijin": [], "nanjo": [], "nishihara": [], "ogimi": [], "okinawa": [], "onna": [], "shimoji": [], "taketomi": [], "tarama": [], "tokashiki": [], "tomigusuku": [], "tonaki": [], "urasoe": [], "uruma": [], "yaese": [], "yomitan": [], "yonabaru": [], "yonaguni": [], "zamami": []], "osaka": ["abeno": [], "chihayaakasaka": [], "chuo": [], "daito": [], "fujiidera": [], "habikino": [], "hannan": [], "higashiosaka": [], "higashisumiyoshi": [], "higashiyodogawa": [], "hirakata": [], "ibaraki": [], "ikeda": [], "izumi": [], "izumiotsu": [], "izumisano": [], "kadoma": [], "kaizuka": [], "kanan": [], "kashiwara": [], "katano": [], "kawachinagano": [], "kishiwada": [], "kita": [], "kumatori": [], "matsubara": [], "minato": [], "minoh": [], "misaki": [], "moriguchi": [], "neyagawa": [], "nishi": [], "nose": [], "osakasayama": [], "sakai": [], "sayama": [], "sennan": [], "settsu": [], "shijonawate": [], "shimamoto": [], "suita": [], "tadaoka": [], "taishi": [], "tajiri": [], "takaishi": [], "takatsuki": [], "tondabayashi": [], "toyonaka": [], "toyono": [], "yao": []], "saga": ["ariake": [], "arita": [], "fukudomi": [], "genkai": [], "hamatama": [], "hizen": [], "imari": [], "kamimine": [], "kanzaki": [], "karatsu": [], "kashima": [], "kitagata": [], "kitahata": [], "kiyama": [], "kouhoku": [], "kyuragi": [], "nishiarita": [], "ogi": [], "omachi": [], "ouchi": [], "saga": [], "shiroishi": [], "taku": [], "tara": [], "tosu": [], "yoshinogari": []], "saitama": ["arakawa": [], "asaka": [], "chichibu": [], "fujimi": [], "fujimino": [], "fukaya": [], "hanno": [], "hanyu": [], "hasuda": [], "hatogaya": [], "hatoyama": [], "hidaka": [], "higashichichibu": [], "higashimatsuyama": [], "honjo": [], "ina": [], "iruma": [], "iwatsuki": [], "kamiizumi": [], "kamikawa": [], "kamisato": [], "kasukabe": [], "kawagoe": [], "kawaguchi": [], "kawajima": [], "kazo": [], "kitamoto": [], "koshigaya": [], "kounosu": [], "kuki": [], "kumagaya": [], "matsubushi": [], "minano": [], "misato": [], "miyashiro": [], "miyoshi": [], "moroyama": [], "nagatoro": [], "namegawa": [], "niiza": [], "ogano": [], "ogawa": [], "ogose": [], "okegawa": [], "omiya": [], "otaki": [], "ranzan": [], "ryokami": [], "saitama": [], "sakado": [], "satte": [], "sayama": [], "shiki": [], "shiraoka": [], "soka": [], "sugito": [], "toda": [], "tokigawa": [], "tokorozawa": [], "tsurugashima": [], "urawa": [], "warabi": [], "yashio": [], "yokoze": [], "yono": [], "yorii": [], "yoshida": [], "yoshikawa": [], "yoshimi": []], "shiga": ["aisho": [], "gamo": [], "higashiomi": [], "hikone": [], "koka": [], "konan": [], "kosei": [], "koto": [], "kusatsu": [], "maibara": [], "moriyama": [], "nagahama": [], "nishiazai": [], "notogawa": [], "omihachiman": [], "otsu": [], "ritto": [], "ryuoh": [], "takashima": [], "takatsuki": [], "torahime": [], "toyosato": [], "yasu": []], "shimane": ["akagi": [], "ama": [], "gotsu": [], "hamada": [], "higashiizumo": [], "hikawa": [], "hikimi": [], "izumo": [], "kakinoki": [], "masuda": [], "matsue": [], "misato": [], "nishinoshima": [], "ohda": [], "okinoshima": [], "okuizumo": [], "shimane": [], "tamayu": [], "tsuwano": [], "unnan": [], "yakumo": [], "yasugi": [], "yatsuka": []], "shizuoka": ["arai": [], "atami": [], "fuji": [], "fujieda": [], "fujikawa": [], "fujinomiya": [], "fukuroi": [], "gotemba": [], "haibara": [], "hamamatsu": [], "higashiizu": [], "ito": [], "iwata": [], "izu": [], "izunokuni": [], "kakegawa": [], "kannami": [], "kawanehon": [], "kawazu": [], "kikugawa": [], "kosai": [], "makinohara": [], "matsuzaki": [], "minamiizu": [], "mishima": [], "morimachi": [], "nishiizu": [], "numazu": [], "omaezaki": [], "shimada": [], "shimizu": [], "shimoda": [], "shizuoka": [], "susono": [], "yaizu": [], "yoshida": []], "tochigi": ["ashikaga": [], "bato": [], "haga": [], "ichikai": [], "iwafune": [], "kaminokawa": [], "kanuma": [], "karasuyama": [], "kuroiso": [], "mashiko": [], "mibu": [], "moka": [], "motegi": [], "nasu": [], "nasushiobara": [], "nikko": [], "nishikata": [], "nogi": [], "ohira": [], "ohtawara": [], "oyama": [], "sakura": [], "sano": [], "shimotsuke": [], "shioya": [], "takanezawa": [], "tochigi": [], "tsuga": [], "ujiie": [], "utsunomiya": [], "yaita": []], "tokushima": ["aizumi": [], "anan": [], "ichiba": [], "itano": [], "kainan": [], "komatsushima": [], "matsushige": [], "mima": [], "minami": [], "miyoshi": [], "mugi": [], "nakagawa": [], "naruto": [], "sanagochi": [], "shishikui": [], "tokushima": [], "wajiki": []], "tokyo": ["adachi": [], "akiruno": [], "akishima": [], "aogashima": [], "arakawa": [], "bunkyo": [], "chiyoda": [], "chofu": [], "chuo": [], "edogawa": [], "fuchu": [], "fussa": [], "hachijo": [], "hachioji": [], "hamura": [], "higashikurume": [], "higashimurayama": [], "higashiyamato": [], "hino": [], "hinode": [], "hinohara": [], "inagi": [], "itabashi": [], "katsushika": [], "kita": [], "kiyose": [], "kodaira": [], "koganei": [], "kokubunji": [], "komae": [], "koto": [], "kouzushima": [], "kunitachi": [], "machida": [], "meguro": [], "minato": [], "mitaka": [], "mizuho": [], "musashimurayama": [], "musashino": [], "nakano": [], "nerima": [], "ogasawara": [], "okutama": [], "ome": [], "oshima": [], "ota": [], "setagaya": [], "shibuya": [], "shinagawa": [], "shinjuku": [], "suginami": [], "sumida": [], "tachikawa": [], "taito": [], "tama": [], "toshima": []], "tottori": ["chizu": [], "hino": [], "kawahara": [], "koge": [], "kotoura": [], "misasa": [], "nanbu": [], "nichinan": [], "sakaiminato": [], "tottori": [], "wakasa": [], "yazu": [], "yonago": []], "toyama": ["asahi": [], "fuchu": [], "fukumitsu": [], "funahashi": [], "himi": [], "imizu": [], "inami": [], "johana": [], "kamiichi": [], "kurobe": [], "nakaniikawa": [], "namerikawa": [], "nanto": [], "nyuzen": [], "oyabe": [], "taira": [], "takaoka": [], "tateyama": [], "toga": [], "tonami": [], "toyama": [], "unazuki": [], "uozu": [], "yamada": []], "wakayama": ["arida": [], "aridagawa": [], "gobo": [], "hashimoto": [], "hidaka": [], "hirogawa": [], "inami": [], "iwade": [], "kainan": [], "kamitonda": [], "katsuragi": [], "kimino": [], "kinokawa": [], "kitayama": [], "koya": [], "koza": [], "kozagawa": [], "kudoyama": [], "kushimoto": [], "mihama": [], "misato": [], "nachikatsuura": [], "shingu": [], "shirahama": [], "taiji": [], "tanabe": [], "wakayama": [], "yuasa": [], "yura": []], "yamagata": ["asahi": [], "funagata": [], "higashine": [], "iide": [], "kahoku": [], "kaminoyama": [], "kaneyama": [], "kawanishi": [], "mamurogawa": [], "mikawa": [], "murayama": [], "nagai": [], "nakayama": [], "nanyo": [], "nishikawa": [], "obanazawa": [], "oe": [], "oguni": [], "ohkura": [], "oishida": [], "sagae": [], "sakata": [], "sakegawa": [], "shinjo": [], "shirataka": [], "shonai": [], "takahata": [], "tendo": [], "tozawa": [], "tsuruoka": [], "yamagata": [], "yamanobe": [], "yonezawa": [], "yuza": []], "yamaguchi": ["abu": [], "hagi": [], "hikari": [], "hofu": [], "iwakuni": [], "kudamatsu": [], "mitou": [], "nagato": [], "oshima": [], "shimonoseki": [], "shunan": [], "tabuse": [], "tokuyama": [], "toyota": [], "ube": [], "yuu": []], "yamanashi": ["chuo": [], "doshi": [], "fuefuki": [], "fujikawa": [], "fujikawaguchiko": [], "fujiyoshida": [], "hayakawa": [], "hokuto": [], "ichikawamisato": [], "kai": [], "kofu": [], "koshu": [], "kosuge": [], "minami-alps": [], "minobu": [], "nakamichi": [], "nanbu": [], "narusawa": [], "nirasaki": [], "nishikatsura": [], "oshino": [], "otsuki": [], "showa": [], "tabayama": [], "tsuru": [], "uenohara": [], "yamanakako": [], "yamanashi": []], "xn--4pvxs": [], "xn--vgu402c": [], "xn--c3s14m": [], "xn--f6qx53a": [], "xn--8pvr4u": [], "xn--uist22h": [], "xn--djrs72d6uy": [], "xn--mkru45i": [], "xn--0trq7p7nn": [], "xn--8ltr62k": [], "xn--2m4a15e": [], "xn--efvn9s": [], "xn--32vp30h": [], "xn--4it797k": [], "xn--1lqs71d": [], "xn--5rtp49c": [], "xn--5js045d": [], "xn--ehqz56n": [], "xn--1lqs03n": [], "xn--qqqt11m": [], "xn--kbrq7o": [], "xn--pssu33l": [], "xn--ntsq17g": [], "xn--uisz3g": [], "xn--6btw5a": [], "xn--1ctwo": [], "xn--6orx2r": [], "xn--rht61e": [], "xn--rht27z": [], "xn--djty4k": [], "xn--nit225k": [], "xn--rht3d": [], "xn--klty5x": [], "xn--kltx9a": [], "xn--kltp7d": [], "xn--uuwu58a": [], "xn--zbx025d": [], "xn--ntso0iqx3a": [], "xn--elqq16h": [], "xn--4it168d": [], "xn--klt787d": [], "xn--rny31h": [], "xn--7t0a264c": [], "xn--5rtq34k": [], "xn--k7yn95e": [], "xn--tor131o": [], "xn--d5qv7z876c": [], "kawasaki": ["*": [], "city": ["!": []]], "kitakyushu": ["*": [], "city": ["!": []]], "kobe": ["*": [], "city": ["!": []]], "nagoya": ["*": [], "city": ["!": []]], "sapporo": ["*": [], "city": ["!": []]], "sendai": ["*": [], "city": ["!": []]], "yokohama": ["*": [], "city": ["!": []]]], "ke": ["ac": [], "co": [], "go": [], "info": [], "me": [], "mobi": [], "ne": [], "or": [], "sc": []], "kg": ["org": [], "net": [], "com": [], "edu": [], "gov": [], "mil": []], "kh": ["*": []], "ki": ["edu": [], "biz": [], "net": [], "org": [], "gov": [], "info": [], "com": []], "km": ["org": [], "nom": [], "gov": [], "prd": [], "tm": [], "edu": [], "mil": [], "ass": [], "com": [], "coop": [], "asso": [], "presse": [], "medecin": [], "notaires": [], "pharmaciens": [], "veterinaire": [], "gouv": []], "kn": ["net": [], "org": [], "edu": [], "gov": []], "kp": ["com": [], "edu": [], "gov": [], "org": [], "rep": [], "tra": []], "kr": ["ac": [], "co": [], "es": [], "go": [], "hs": [], "kg": [], "mil": [], "ms": [], "ne": [], "or": [], "pe": [], "re": [], "sc": [], "busan": [], "chungbuk": [], "chungnam": [], "daegu": [], "daejeon": [], "gangwon": [], "gwangju": [], "gyeongbuk": [], "gyeonggi": [], "gyeongnam": [], "incheon": [], "jeju": [], "jeonbuk": [], "jeonnam": [], "seoul": [], "ulsan": []], "kw": ["*": []], "ky": ["edu": [], "gov": [], "com": [], "org": [], "net": []], "kz": ["org": [], "edu": [], "net": [], "gov": [], "mil": [], "com": []], "la": ["int": [], "net": [], "info": [], "edu": [], "gov": [], "per": [], "com": [], "org": []], "lb": ["com": [], "edu": [], "gov": [], "net": [], "org": []], "lc": ["com": [], "net": [], "co": [], "org": [], "edu": [], "gov": []], "li": [], "lk": ["gov": [], "sch": [], "net": [], "int": [], "com": [], "org": [], "edu": [], "ngo": [], "soc": [], "web": [], "ltd": [], "assn": [], "grp": [], "hotel": [], "ac": []], "lr": ["com": [], "edu": [], "gov": [], "org": [], "net": []], "ls": ["co": [], "org": []], "lt": ["gov": []], "lu": [], "lv": ["com": [], "edu": [], "gov": [], "org": [], "mil": [], "id": [], "net": [], "asn": [], "conf": []], "ly": ["com": [], "net": [], "gov": [], "plc": [], "edu": [], "sch": [], "med": [], "org": [], "id": []], "ma": ["co": [], "net": [], "gov": [], "org": [], "ac": [], "press": []], "mc": ["tm": [], "asso": []], "md": [], "me": ["co": [], "net": [], "org": [], "edu": [], "ac": [], "gov": [], "its": [], "priv": []], "mg": ["org": [], "nom": [], "gov": [], "prd": [], "tm": [], "edu": [], "mil": [], "com": [], "co": []], "mh": [], "mil": [], "mk": ["com": [], "org": [], "net": [], "edu": [], "gov": [], "inf": [], "name": []], "ml": ["com": [], "edu": [], "gouv": [], "gov": [], "net": [], "org": [], "presse": []], "mm": ["*": []], "mn": ["gov": [], "edu": [], "org": []], "mo": ["com": [], "net": [], "org": [], "edu": [], "gov": []], "mobi": [], "mp": [], "mq": [], "mr": ["gov": []], "ms": ["com": [], "edu": [], "gov": [], "net": [], "org": []], "mt": ["com": [], "edu": [], "net": [], "org": []], "mu": ["com": [], "net": [], "org": [], "gov": [], "ac": [], "co": [], "or": []], "museum": ["academy": [], "agriculture": [], "air": [], "airguard": [], "alabama": [], "alaska": [], "amber": [], "ambulance": [], "american": [], "americana": [], "americanantiques": [], "americanart": [], "amsterdam": [], "and": [], "annefrank": [], "anthro": [], "anthropology": [], "antiques": [], "aquarium": [], "arboretum": [], "archaeological": [], "archaeology": [], "architecture": [], "art": [], "artanddesign": [], "artcenter": [], "artdeco": [], "arteducation": [], "artgallery": [], "arts": [], "artsandcrafts": [], "asmatart": [], "assassination": [], "assisi": [], "association": [], "astronomy": [], "atlanta": [], "austin": [], "australia": [], "automotive": [], "aviation": [], "axis": [], "badajoz": [], "baghdad": [], "bahn": [], "bale": [], "baltimore": [], "barcelona": [], "baseball": [], "basel": [], "baths": [], "bauern": [], "beauxarts": [], "beeldengeluid": [], "bellevue": [], "bergbau": [], "berkeley": [], "berlin": [], "bern": [], "bible": [], "bilbao": [], "bill": [], "birdart": [], "birthplace": [], "bonn": [], "boston": [], "botanical": [], "botanicalgarden": [], "botanicgarden": [], "botany": [], "brandywinevalley": [], "brasil": [], "bristol": [], "british": [], "britishcolumbia": [], "broadcast": [], "brunel": [], "brussel": [], "brussels": [], "bruxelles": [], "building": [], "burghof": [], "bus": [], "bushey": [], "cadaques": [], "california": [], "cambridge": [], "can": [], "canada": [], "capebreton": [], "carrier": [], "cartoonart": [], "casadelamoneda": [], "castle": [], "castres": [], "celtic": [], "center": [], "chattanooga": [], "cheltenham": [], "chesapeakebay": [], "chicago": [], "children": [], "childrens": [], "childrensgarden": [], "chiropractic": [], "chocolate": [], "christiansburg": [], "cincinnati": [], "cinema": [], "circus": [], "civilisation": [], "civilization": [], "civilwar": [], "clinton": [], "clock": [], "coal": [], "coastaldefence": [], "cody": [], "coldwar": [], "collection": [], "colonialwilliamsburg": [], "coloradoplateau": [], "columbia": [], "columbus": [], "communication": [], "communications": [], "community": [], "computer": [], "computerhistory": [], "xn--comunicaes-v6a2o": [], "contemporary": [], "contemporaryart": [], "convent": [], "copenhagen": [], "corporation": [], "xn--correios-e-telecomunicaes-ghc29a": [], "corvette": [], "costume": [], "countryestate": [], "county": [], "crafts": [], "cranbrook": [], "creation": [], "cultural": [], "culturalcenter": [], "culture": [], "cyber": [], "cymru": [], "dali": [], "dallas": [], "database": [], "ddr": [], "decorativearts": [], "delaware": [], "delmenhorst": [], "denmark": [], "depot": [], "design": [], "detroit": [], "dinosaur": [], "discovery": [], "dolls": [], "donostia": [], "durham": [], "eastafrica": [], "eastcoast": [], "education": [], "educational": [], "egyptian": [], "eisenbahn": [], "elburg": [], "elvendrell": [], "embroidery": [], "encyclopedic": [], "england": [], "entomology": [], "environment": [], "environmentalconservation": [], "epilepsy": [], "essex": [], "estate": [], "ethnology": [], "exeter": [], "exhibition": [], "family": [], "farm": [], "farmequipment": [], "farmers": [], "farmstead": [], "field": [], "figueres": [], "filatelia": [], "film": [], "fineart": [], "finearts": [], "finland": [], "flanders": [], "florida": [], "force": [], "fortmissoula": [], "fortworth": [], "foundation": [], "francaise": [], "frankfurt": [], "franziskaner": [], "freemasonry": [], "freiburg": [], "fribourg": [], "frog": [], "fundacio": [], "furniture": [], "gallery": [], "garden": [], "gateway": [], "geelvinck": [], "gemological": [], "geology": [], "georgia": [], "giessen": [], "glas": [], "glass": [], "gorge": [], "grandrapids": [], "graz": [], "guernsey": [], "halloffame": [], "hamburg": [], "handson": [], "harvestcelebration": [], "hawaii": [], "health": [], "heimatunduhren": [], "hellas": [], "helsinki": [], "hembygdsforbund": [], "heritage": [], "histoire": [], "historical": [], "historicalsociety": [], "historichouses": [], "historisch": [], "historisches": [], "history": [], "historyofscience": [], "horology": [], "house": [], "humanities": [], "illustration": [], "imageandsound": [], "indian": [], "indiana": [], "indianapolis": [], "indianmarket": [], "intelligence": [], "interactive": [], "iraq": [], "iron": [], "isleofman": [], "jamison": [], "jefferson": [], "jerusalem": [], "jewelry": [], "jewish": [], "jewishart": [], "jfk": [], "journalism": [], "judaica": [], "judygarland": [], "juedisches": [], "juif": [], "karate": [], "karikatur": [], "kids": [], "koebenhavn": [], "koeln": [], "kunst": [], "kunstsammlung": [], "kunstunddesign": [], "labor": [], "labour": [], "lajolla": [], "lancashire": [], "landes": [], "lans": [], "xn--lns-qla": [], "larsson": [], "lewismiller": [], "lincoln": [], "linz": [], "living": [], "livinghistory": [], "localhistory": [], "london": [], "losangeles": [], "louvre": [], "loyalist": [], "lucerne": [], "luxembourg": [], "luzern": [], "mad": [], "madrid": [], "mallorca": [], "manchester": [], "mansion": [], "mansions": [], "manx": [], "marburg": [], "maritime": [], "maritimo": [], "maryland": [], "marylhurst": [], "media": [], "medical": [], "medizinhistorisches": [], "meeres": [], "memorial": [], "mesaverde": [], "michigan": [], "midatlantic": [], "military": [], "mill": [], "miners": [], "mining": [], "minnesota": [], "missile": [], "missoula": [], "modern": [], "moma": [], "money": [], "monmouth": [], "monticello": [], "montreal": [], "moscow": [], "motorcycle": [], "muenchen": [], "muenster": [], "mulhouse": [], "muncie": [], "museet": [], "museumcenter": [], "museumvereniging": [], "music": [], "national": [], "nationalfirearms": [], "nationalheritage": [], "nativeamerican": [], "naturalhistory": [], "naturalhistorymuseum": [], "naturalsciences": [], "nature": [], "naturhistorisches": [], "natuurwetenschappen": [], "naumburg": [], "naval": [], "nebraska": [], "neues": [], "newhampshire": [], "newjersey": [], "newmexico": [], "newport": [], "newspaper": [], "newyork": [], "niepce": [], "norfolk": [], "north": [], "nrw": [], "nuernberg": [], "nuremberg": [], "nyc": [], "nyny": [], "oceanographic": [], "oceanographique": [], "omaha": [], "online": [], "ontario": [], "openair": [], "oregon": [], "oregontrail": [], "otago": [], "oxford": [], "pacific": [], "paderborn": [], "palace": [], "paleo": [], "palmsprings": [], "panama": [], "paris": [], "pasadena": [], "pharmacy": [], "philadelphia": [], "philadelphiaarea": [], "philately": [], "phoenix": [], "photography": [], "pilots": [], "pittsburgh": [], "planetarium": [], "plantation": [], "plants": [], "plaza": [], "portal": [], "portland": [], "portlligat": [], "posts-and-telecommunications": [], "preservation": [], "presidio": [], "press": [], "project": [], "public": [], "pubol": [], "quebec": [], "railroad": [], "railway": [], "research": [], "resistance": [], "riodejaneiro": [], "rochester": [], "rockart": [], "roma": [], "russia": [], "saintlouis": [], "salem": [], "salvadordali": [], "salzburg": [], "sandiego": [], "sanfrancisco": [], "santabarbara": [], "santacruz": [], "santafe": [], "saskatchewan": [], "satx": [], "savannahga": [], "schlesisches": [], "schoenbrunn": [], "schokoladen": [], "school": [], "schweiz": [], "science": [], "scienceandhistory": [], "scienceandindustry": [], "sciencecenter": [], "sciencecenters": [], "science-fiction": [], "sciencehistory": [], "sciences": [], "sciencesnaturelles": [], "scotland": [], "seaport": [], "settlement": [], "settlers": [], "shell": [], "sherbrooke": [], "sibenik": [], "silk": [], "ski": [], "skole": [], "society": [], "sologne": [], "soundandvision": [], "southcarolina": [], "southwest": [], "space": [], "spy": [], "square": [], "stadt": [], "stalbans": [], "starnberg": [], "state": [], "stateofdelaware": [], "station": [], "steam": [], "steiermark": [], "stjohn": [], "stockholm": [], "stpetersburg": [], "stuttgart": [], "suisse": [], "surgeonshall": [], "surrey": [], "svizzera": [], "sweden": [], "sydney": [], "tank": [], "tcm": [], "technology": [], "telekommunikation": [], "television": [], "texas": [], "textile": [], "theater": [], "time": [], "timekeeping": [], "topology": [], "torino": [], "touch": [], "town": [], "transport": [], "tree": [], "trolley": [], "trust": [], "trustee": [], "uhren": [], "ulm": [], "undersea": [], "university": [], "usa": [], "usantiques": [], "usarts": [], "uscountryestate": [], "usculture": [], "usdecorativearts": [], "usgarden": [], "ushistory": [], "ushuaia": [], "uslivinghistory": [], "utah": [], "uvic": [], "valley": [], "vantaa": [], "versailles": [], "viking": [], "village": [], "virginia": [], "virtual": [], "virtuel": [], "vlaanderen": [], "volkenkunde": [], "wales": [], "wallonie": [], "war": [], "washingtondc": [], "watchandclock": [], "watch-and-clock": [], "western": [], "westfalen": [], "whaling": [], "wildlife": [], "williamsburg": [], "windmill": [], "workshop": [], "york": [], "yorkshire": [], "yosemite": [], "youth": [], "zoological": [], "zoology": [], "xn--9dbhblg6di": [], "xn--h1aegh": []], "mv": ["aero": [], "biz": [], "com": [], "coop": [], "edu": [], "gov": [], "info": [], "int": [], "mil": [], "museum": [], "name": [], "net": [], "org": [], "pro": []], "mw": ["ac": [], "biz": [], "co": [], "com": [], "coop": [], "edu": [], "gov": [], "int": [], "museum": [], "net": [], "org": []], "mx": ["com": [], "org": [], "gob": [], "edu": [], "net": []], "my": ["com": [], "net": [], "org": [], "gov": [], "edu": [], "mil": [], "name": []], "mz": ["ac": [], "adv": [], "co": [], "edu": [], "gov": [], "mil": [], "net": [], "org": []], "na": ["info": [], "pro": [], "name": [], "school": [], "or": [], "dr": [], "us": [], "mx": [], "ca": [], "in": [], "cc": [], "tv": [], "ws": [], "mobi": [], "co": [], "com": [], "org": []], "name": [], "nc": ["asso": [], "nom": []], "ne": [], "net": [], "nf": ["com": [], "net": [], "per": [], "rec": [], "web": [], "arts": [], "firm": [], "info": [], "other": [], "store": []], "ng": ["com": [], "edu": [], "gov": [], "i": [], "mil": [], "mobi": [], "name": [], "net": [], "org": [], "sch": []], "ni": ["ac": [], "biz": [], "co": [], "com": [], "edu": [], "gob": [], "in": [], "info": [], "int": [], "mil": [], "net": [], "nom": [], "org": [], "web": []], "nl": ["bv": []], "no": ["fhs": [], "vgs": [], "fylkesbibl": [], "folkebibl": [], "museum": [], "idrett": [], "priv": [], "mil": [], "stat": [], "dep": [], "kommune": [], "herad": [], "aa": ["gs": []], "ah": ["gs": []], "bu": ["gs": []], "fm": ["gs": []], "hl": ["gs": []], "hm": ["gs": []], "jan-mayen": ["gs": []], "mr": ["gs": []], "nl": ["gs": []], "nt": ["gs": []], "of": ["gs": []], "ol": ["gs": []], "oslo": ["gs": []], "rl": ["gs": []], "sf": ["gs": []], "st": ["gs": []], "svalbard": ["gs": []], "tm": ["gs": []], "tr": ["gs": []], "va": ["gs": []], "vf": ["gs": []], "akrehamn": [], "xn--krehamn-dxa": [], "algard": [], "xn--lgrd-poac": [], "arna": [], "brumunddal": [], "bryne": [], "bronnoysund": [], "xn--brnnysund-m8ac": [], "drobak": [], "xn--drbak-wua": [], "egersund": [], "fetsund": [], "floro": [], "xn--flor-jra": [], "fredrikstad": [], "hokksund": [], "honefoss": [], "xn--hnefoss-q1a": [], "jessheim": [], "jorpeland": [], "xn--jrpeland-54a": [], "kirkenes": [], "kopervik": [], "krokstadelva": [], "langevag": [], "xn--langevg-jxa": [], "leirvik": [], "mjondalen": [], "xn--mjndalen-64a": [], "mo-i-rana": [], "mosjoen": [], "xn--mosjen-eya": [], "nesoddtangen": [], "orkanger": [], "osoyro": [], "xn--osyro-wua": [], "raholt": [], "xn--rholt-mra": [], "sandnessjoen": [], "xn--sandnessjen-ogb": [], "skedsmokorset": [], "slattum": [], "spjelkavik": [], "stathelle": [], "stavern": [], "stjordalshalsen": [], "xn--stjrdalshalsen-sqb": [], "tananger": [], "tranby": [], "vossevangen": [], "afjord": [], "xn--fjord-lra": [], "agdenes": [], "al": [], "xn--l-1fa": [], "alesund": [], "xn--lesund-hua": [], "alstahaug": [], "alta": [], "xn--lt-liac": [], "alaheadju": [], "xn--laheadju-7ya": [], "alvdal": [], "amli": [], "xn--mli-tla": [], "amot": [], "xn--mot-tla": [], "andebu": [], "andoy": [], "xn--andy-ira": [], "andasuolo": [], "ardal": [], "xn--rdal-poa": [], "aremark": [], "arendal": [], "xn--s-1fa": [], "aseral": [], "xn--seral-lra": [], "asker": [], "askim": [], "askvoll": [], "askoy": [], "xn--asky-ira": [], "asnes": [], "xn--snes-poa": [], "audnedaln": [], "aukra": [], "aure": [], "aurland": [], "aurskog-holand": [], "xn--aurskog-hland-jnb": [], "austevoll": [], "austrheim": [], "averoy": [], "xn--avery-yua": [], "balestrand": [], "ballangen": [], "balat": [], "xn--blt-elab": [], "balsfjord": [], "bahccavuotna": [], "xn--bhccavuotna-k7a": [], "bamble": [], "bardu": [], "beardu": [], "beiarn": [], "bajddar": [], "xn--bjddar-pta": [], "baidar": [], "xn--bidr-5nac": [], "berg": [], "bergen": [], "berlevag": [], "xn--berlevg-jxa": [], "bearalvahki": [], "xn--bearalvhki-y4a": [], "bindal": [], "birkenes": [], "bjarkoy": [], "xn--bjarky-fya": [], "bjerkreim": [], "bjugn": [], "bodo": [], "xn--bod-2na": [], "badaddja": [], "xn--bdddj-mrabd": [], "budejju": [], "bokn": [], "bremanger": [], "bronnoy": [], "xn--brnny-wuac": [], "bygland": [], "bykle": [], "barum": [], "xn--brum-voa": [], "telemark": ["bo": [], "xn--b-5ga": []], "nordland": ["bo": [], "xn--b-5ga": [], "heroy": [], "xn--hery-ira": []], "bievat": [], "xn--bievt-0qa": [], "bomlo": [], "xn--bmlo-gra": [], "batsfjord": [], "xn--btsfjord-9za": [], "bahcavuotna": [], "xn--bhcavuotna-s4a": [], "dovre": [], "drammen": [], "drangedal": [], "dyroy": [], "xn--dyry-ira": [], "donna": [], "xn--dnna-gra": [], "eid": [], "eidfjord": [], "eidsberg": [], "eidskog": [], "eidsvoll": [], "eigersund": [], "elverum": [], "enebakk": [], "engerdal": [], "etne": [], "etnedal": [], "evenes": [], "evenassi": [], "xn--eveni-0qa01ga": [], "evje-og-hornnes": [], "farsund": [], "fauske": [], "fuossko": [], "fuoisku": [], "fedje": [], "fet": [], "finnoy": [], "xn--finny-yua": [], "fitjar": [], "fjaler": [], "fjell": [], "flakstad": [], "flatanger": [], "flekkefjord": [], "flesberg": [], "flora": [], "fla": [], "xn--fl-zia": [], "folldal": [], "forsand": [], "fosnes": [], "frei": [], "frogn": [], "froland": [], "frosta": [], "frana": [], "xn--frna-woa": [], "froya": [], "xn--frya-hra": [], "fusa": [], "fyresdal": [], "forde": [], "xn--frde-gra": [], "gamvik": [], "gangaviika": [], "xn--ggaviika-8ya47h": [], "gaular": [], "gausdal": [], "gildeskal": [], "xn--gildeskl-g0a": [], "giske": [], "gjemnes": [], "gjerdrum": [], "gjerstad": [], "gjesdal": [], "gjovik": [], "xn--gjvik-wua": [], "gloppen": [], "gol": [], "gran": [], "grane": [], "granvin": [], "gratangen": [], "grimstad": [], "grong": [], "kraanghke": [], "xn--kranghke-b0a": [], "grue": [], "gulen": [], "hadsel": [], "halden": [], "halsa": [], "hamar": [], "hamaroy": [], "habmer": [], "xn--hbmer-xqa": [], "hapmir": [], "xn--hpmir-xqa": [], "hammerfest": [], "hammarfeasta": [], "xn--hmmrfeasta-s4ac": [], "haram": [], "hareid": [], "harstad": [], "hasvik": [], "aknoluokta": [], "xn--koluokta-7ya57h": [], "hattfjelldal": [], "aarborte": [], "haugesund": [], "hemne": [], "hemnes": [], "hemsedal": [], "more-og-romsdal": ["heroy": [], "sande": []], "xn--mre-og-romsdal-qqb": ["xn--hery-ira": [], "sande": []], "hitra": [], "hjartdal": [], "hjelmeland": [], "hobol": [], "xn--hobl-ira": [], "hof": [], "hol": [], "hole": [], "holmestrand": [], "holtalen": [], "xn--holtlen-hxa": [], "hornindal": [], "horten": [], "hurdal": [], "hurum": [], "hvaler": [], "hyllestad": [], "hagebostad": [], "xn--hgebostad-g3a": [], "hoyanger": [], "xn--hyanger-q1a": [], "hoylandet": [], "xn--hylandet-54a": [], "ha": [], "xn--h-2fa": [], "ibestad": [], "inderoy": [], "xn--indery-fya": [], "iveland": [], "jevnaker": [], "jondal": [], "jolster": [], "xn--jlster-bya": [], "karasjok": [], "karasjohka": [], "xn--krjohka-hwab49j": [], "karlsoy": [], "galsa": [], "xn--gls-elac": [], "karmoy": [], "xn--karmy-yua": [], "kautokeino": [], "guovdageaidnu": [], "klepp": [], "klabu": [], "xn--klbu-woa": [], "kongsberg": [], "kongsvinger": [], "kragero": [], "xn--krager-gya": [], "kristiansand": [], "kristiansund": [], "krodsherad": [], "xn--krdsherad-m8a": [], "kvalsund": [], "rahkkeravju": [], "xn--rhkkervju-01af": [], "kvam": [], "kvinesdal": [], "kvinnherad": [], "kviteseid": [], "kvitsoy": [], "xn--kvitsy-fya": [], "kvafjord": [], "xn--kvfjord-nxa": [], "giehtavuoatna": [], "kvanangen": [], "xn--kvnangen-k0a": [], "navuotna": [], "xn--nvuotna-hwa": [], "kafjord": [], "xn--kfjord-iua": [], "gaivuotna": [], "xn--givuotna-8ya": [], "larvik": [], "lavangen": [], "lavagis": [], "loabat": [], "xn--loabt-0qa": [], "lebesby": [], "davvesiida": [], "leikanger": [], "leirfjord": [], "leka": [], "leksvik": [], "lenvik": [], "leangaviika": [], "xn--leagaviika-52b": [], "lesja": [], "levanger": [], "lier": [], "lierne": [], "lillehammer": [], "lillesand": [], "lindesnes": [], "lindas": [], "xn--linds-pra": [], "lom": [], "loppa": [], "lahppi": [], "xn--lhppi-xqa": [], "lund": [], "lunner": [], "luroy": [], "xn--lury-ira": [], "luster": [], "lyngdal": [], "lyngen": [], "ivgu": [], "lardal": [], "lerdal": [], "xn--lrdal-sra": [], "lodingen": [], "xn--ldingen-q1a": [], "lorenskog": [], "xn--lrenskog-54a": [], "loten": [], "xn--lten-gra": [], "malvik": [], "masoy": [], "xn--msy-ula0h": [], "muosat": [], "xn--muost-0qa": [], "mandal": [], "marker": [], "marnardal": [], "masfjorden": [], "meland": [], "meldal": [], "melhus": [], "meloy": [], "xn--mely-ira": [], "meraker": [], "xn--merker-kua": [], "moareke": [], "xn--moreke-jua": [], "midsund": [], "midtre-gauldal": [], "modalen": [], "modum": [], "molde": [], "moskenes": [], "moss": [], "mosvik": [], "malselv": [], "xn--mlselv-iua": [], "malatvuopmi": [], "xn--mlatvuopmi-s4a": [], "namdalseid": [], "aejrie": [], "namsos": [], "namsskogan": [], "naamesjevuemie": [], "xn--nmesjevuemie-tcba": [], "laakesvuemie": [], "nannestad": [], "narvik": [], "narviika": [], "naustdal": [], "nedre-eiker": [], "akershus": ["nes": []], "buskerud": ["nes": []], "nesna": [], "nesodden": [], "nesseby": [], "unjarga": [], "xn--unjrga-rta": [], "nesset": [], "nissedal": [], "nittedal": [], "nord-aurdal": [], "nord-fron": [], "nord-odal": [], "norddal": [], "nordkapp": [], "davvenjarga": [], "xn--davvenjrga-y4a": [], "nordre-land": [], "nordreisa": [], "raisa": [], "xn--risa-5na": [], "nore-og-uvdal": [], "notodden": [], "naroy": [], "xn--nry-yla5g": [], "notteroy": [], "xn--nttery-byae": [], "odda": [], "oksnes": [], "xn--ksnes-uua": [], "oppdal": [], "oppegard": [], "xn--oppegrd-ixa": [], "orkdal": [], "orland": [], "xn--rland-uua": [], "orskog": [], "xn--rskog-uua": [], "orsta": [], "xn--rsta-fra": [], "hedmark": ["os": [], "valer": [], "xn--vler-qoa": []], "hordaland": ["os": []], "osen": [], "osteroy": [], "xn--ostery-fya": [], "ostre-toten": [], "xn--stre-toten-zcb": [], "overhalla": [], "ovre-eiker": [], "xn--vre-eiker-k8a": [], "oyer": [], "xn--yer-zna": [], "oygarden": [], "xn--ygarden-p1a": [], "oystre-slidre": [], "xn--ystre-slidre-ujb": [], "porsanger": [], "porsangu": [], "xn--porsgu-sta26f": [], "porsgrunn": [], "radoy": [], "xn--rady-ira": [], "rakkestad": [], "rana": [], "ruovat": [], "randaberg": [], "rauma": [], "rendalen": [], "rennebu": [], "rennesoy": [], "xn--rennesy-v1a": [], "rindal": [], "ringebu": [], "ringerike": [], "ringsaker": [], "rissa": [], "risor": [], "xn--risr-ira": [], "roan": [], "rollag": [], "rygge": [], "ralingen": [], "xn--rlingen-mxa": [], "rodoy": [], "xn--rdy-0nab": [], "romskog": [], "xn--rmskog-bya": [], "roros": [], "xn--rros-gra": [], "rost": [], "xn--rst-0na": [], "royken": [], "xn--ryken-vua": [], "royrvik": [], "xn--ryrvik-bya": [], "rade": [], "xn--rde-ula": [], "salangen": [], "siellak": [], "saltdal": [], "salat": [], "xn--slt-elab": [], "xn--slat-5na": [], "samnanger": [], "vestfold": ["sande": []], "sandefjord": [], "sandnes": [], "sandoy": [], "xn--sandy-yua": [], "sarpsborg": [], "sauda": [], "sauherad": [], "sel": [], "selbu": [], "selje": [], "seljord": [], "sigdal": [], "siljan": [], "sirdal": [], "skaun": [], "skedsmo": [], "ski": [], "skien": [], "skiptvet": [], "skjervoy": [], "xn--skjervy-v1a": [], "skierva": [], "xn--skierv-uta": [], "skjak": [], "xn--skjk-soa": [], "skodje": [], "skanland": [], "xn--sknland-fxa": [], "skanit": [], "xn--sknit-yqa": [], "smola": [], "xn--smla-hra": [], "snillfjord": [], "snasa": [], "xn--snsa-roa": [], "snoasa": [], "snaase": [], "xn--snase-nra": [], "sogndal": [], "sokndal": [], "sola": [], "solund": [], "songdalen": [], "sortland": [], "spydeberg": [], "stange": [], "stavanger": [], "steigen": [], "steinkjer": [], "stjordal": [], "xn--stjrdal-s1a": [], "stokke": [], "stor-elvdal": [], "stord": [], "stordal": [], "storfjord": [], "omasvuotna": [], "strand": [], "stranda": [], "stryn": [], "sula": [], "suldal": [], "sund": [], "sunndal": [], "surnadal": [], "sveio": [], "svelvik": [], "sykkylven": [], "sogne": [], "xn--sgne-gra": [], "somna": [], "xn--smna-gra": [], "sondre-land": [], "xn--sndre-land-0cb": [], "sor-aurdal": [], "xn--sr-aurdal-l8a": [], "sor-fron": [], "xn--sr-fron-q1a": [], "sor-odal": [], "xn--sr-odal-q1a": [], "sor-varanger": [], "xn--sr-varanger-ggb": [], "matta-varjjat": [], "xn--mtta-vrjjat-k7af": [], "sorfold": [], "xn--srfold-bya": [], "sorreisa": [], "xn--srreisa-q1a": [], "sorum": [], "xn--srum-gra": [], "tana": [], "deatnu": [], "time": [], "tingvoll": [], "tinn": [], "tjeldsund": [], "dielddanuorri": [], "tjome": [], "xn--tjme-hra": [], "tokke": [], "tolga": [], "torsken": [], "tranoy": [], "xn--trany-yua": [], "tromso": [], "xn--troms-zua": [], "tromsa": [], "romsa": [], "trondheim": [], "troandin": [], "trysil": [], "trana": [], "xn--trna-woa": [], "trogstad": [], "xn--trgstad-r1a": [], "tvedestrand": [], "tydal": [], "tynset": [], "tysfjord": [], "divtasvuodna": [], "divttasvuotna": [], "tysnes": [], "tysvar": [], "xn--tysvr-vra": [], "tonsberg": [], "xn--tnsberg-q1a": [], "ullensaker": [], "ullensvang": [], "ulvik": [], "utsira": [], "vadso": [], "xn--vads-jra": [], "cahcesuolo": [], "xn--hcesuolo-7ya35b": [], "vaksdal": [], "valle": [], "vang": [], "vanylven": [], "vardo": [], "xn--vard-jra": [], "varggat": [], "xn--vrggt-xqad": [], "vefsn": [], "vaapste": [], "vega": [], "vegarshei": [], "xn--vegrshei-c0a": [], "vennesla": [], "verdal": [], "verran": [], "vestby": [], "vestnes": [], "vestre-slidre": [], "vestre-toten": [], "vestvagoy": [], "xn--vestvgy-ixa6o": [], "vevelstad": [], "vik": [], "vikna": [], "vindafjord": [], "volda": [], "voss": [], "varoy": [], "xn--vry-yla5g": [], "vagan": [], "xn--vgan-qoa": [], "voagat": [], "vagsoy": [], "xn--vgsy-qoa0j": [], "vaga": [], "xn--vg-yiab": [], "ostfold": ["valer": []], "xn--stfold-9xa": ["xn--vler-qoa": []]], "np": ["*": []], "nr": ["biz": [], "info": [], "gov": [], "edu": [], "org": [], "net": [], "com": []], "nu": [], "nz": ["ac": [], "co": [], "cri": [], "geek": [], "gen": [], "govt": [], "health": [], "iwi": [], "kiwi": [], "maori": [], "mil": [], "xn--mori-qsa": [], "net": [], "org": [], "parliament": [], "school": []], "om": ["co": [], "com": [], "edu": [], "gov": [], "med": [], "museum": [], "net": [], "org": [], "pro": []], "onion": [], "org": [], "pa": ["ac": [], "gob": [], "com": [], "org": [], "sld": [], "edu": [], "net": [], "ing": [], "abo": [], "med": [], "nom": []], "pe": ["edu": [], "gob": [], "nom": [], "mil": [], "org": [], "com": [], "net": []], "pf": ["com": [], "org": [], "edu": []], "pg": ["*": []], "ph": ["com": [], "net": [], "org": [], "gov": [], "edu": [], "ngo": [], "mil": [], "i": []], "pk": ["com": [], "net": [], "edu": [], "org": [], "fam": [], "biz": [], "web": [], "gov": [], "gob": [], "gok": [], "gon": [], "gop": [], "gos": [], "info": []], "pl": ["com": [], "net": [], "org": [], "aid": [], "agro": [], "atm": [], "auto": [], "biz": [], "edu": [], "gmina": [], "gsm": [], "info": [], "mail": [], "miasta": [], "media": [], "mil": [], "nieruchomosci": [], "nom": [], "pc": [], "powiat": [], "priv": [], "realestate": [], "rel": [], "sex": [], "shop": [], "sklep": [], "sos": [], "szkola": [], "targi": [], "tm": [], "tourism": [], "travel": [], "turystyka": [], "gov": ["ap": [], "ic": [], "is": [], "us": [], "kmpsp": [], "kppsp": [], "kwpsp": [], "psp": [], "wskr": [], "kwp": [], "mw": [], "ug": [], "um": [], "umig": [], "ugim": [], "upow": [], "uw": [], "starostwo": [], "pa": [], "po": [], "psse": [], "pup": [], "rzgw": [], "sa": [], "so": [], "sr": [], "wsa": [], "sko": [], "uzs": [], "wiih": [], "winb": [], "pinb": [], "wios": [], "witd": [], "wzmiuw": [], "piw": [], "wiw": [], "griw": [], "wif": [], "oum": [], "sdn": [], "zp": [], "uppo": [], "mup": [], "wuoz": [], "konsulat": [], "oirm": []], "augustow": [], "babia-gora": [], "bedzin": [], "beskidy": [], "bialowieza": [], "bialystok": [], "bielawa": [], "bieszczady": [], "boleslawiec": [], "bydgoszcz": [], "bytom": [], "cieszyn": [], "czeladz": [], "czest": [], "dlugoleka": [], "elblag": [], "elk": [], "glogow": [], "gniezno": [], "gorlice": [], "grajewo": [], "ilawa": [], "jaworzno": [], "jelenia-gora": [], "jgora": [], "kalisz": [], "kazimierz-dolny": [], "karpacz": [], "kartuzy": [], "kaszuby": [], "katowice": [], "kepno": [], "ketrzyn": [], "klodzko": [], "kobierzyce": [], "kolobrzeg": [], "konin": [], "konskowola": [], "kutno": [], "lapy": [], "lebork": [], "legnica": [], "lezajsk": [], "limanowa": [], "lomza": [], "lowicz": [], "lubin": [], "lukow": [], "malbork": [], "malopolska": [], "mazowsze": [], "mazury": [], "mielec": [], "mielno": [], "mragowo": [], "naklo": [], "nowaruda": [], "nysa": [], "olawa": [], "olecko": [], "olkusz": [], "olsztyn": [], "opoczno": [], "opole": [], "ostroda": [], "ostroleka": [], "ostrowiec": [], "ostrowwlkp": [], "pila": [], "pisz": [], "podhale": [], "podlasie": [], "polkowice": [], "pomorze": [], "pomorskie": [], "prochowice": [], "pruszkow": [], "przeworsk": [], "pulawy": [], "radom": [], "rawa-maz": [], "rybnik": [], "rzeszow": [], "sanok": [], "sejny": [], "slask": [], "slupsk": [], "sosnowiec": [], "stalowa-wola": [], "skoczow": [], "starachowice": [], "stargard": [], "suwalki": [], "swidnica": [], "swiebodzin": [], "swinoujscie": [], "szczecin": [], "szczytno": [], "tarnobrzeg": [], "tgory": [], "turek": [], "tychy": [], "ustka": [], "walbrzych": [], "warmia": [], "warszawa": [], "waw": [], "wegrow": [], "wielun": [], "wlocl": [], "wloclawek": [], "wodzislaw": [], "wolomin": [], "wroclaw": [], "zachpomor": [], "zagan": [], "zarow": [], "zgora": [], "zgorzelec": []], "pm": [], "pn": ["gov": [], "co": [], "org": [], "edu": [], "net": []], "post": [], "pr": ["com": [], "net": [], "org": [], "gov": [], "edu": [], "isla": [], "pro": [], "biz": [], "info": [], "name": [], "est": [], "prof": [], "ac": []], "pro": ["aaa": [], "aca": [], "acct": [], "avocat": [], "bar": [], "cpa": [], "eng": [], "jur": [], "law": [], "med": [], "recht": []], "ps": ["edu": [], "gov": [], "sec": [], "plo": [], "com": [], "org": [], "net": []], "pt": ["net": [], "gov": [], "org": [], "edu": [], "int": [], "publ": [], "com": [], "nome": []], "pw": ["co": [], "ne": [], "or": [], "ed": [], "go": [], "belau": []], "py": ["com": [], "coop": [], "edu": [], "gov": [], "mil": [], "net": [], "org": []], "qa": ["com": [], "edu": [], "gov": [], "mil": [], "name": [], "net": [], "org": [], "sch": []], "re": ["asso": [], "com": [], "nom": []], "ro": ["arts": [], "com": [], "firm": [], "info": [], "nom": [], "nt": [], "org": [], "rec": [], "store": [], "tm": [], "www": []], "rs": ["ac": [], "co": [], "edu": [], "gov": [], "in": [], "org": []], "ru": ["ac": [], "edu": [], "gov": [], "int": [], "mil": [], "test": []], "rw": ["gov": [], "net": [], "edu": [], "ac": [], "com": [], "co": [], "int": [], "mil": [], "gouv": []], "sa": ["com": [], "net": [], "org": [], "gov": [], "med": [], "pub": [], "edu": [], "sch": []], "sb": ["com": [], "edu": [], "gov": [], "net": [], "org": []], "sc": ["com": [], "gov": [], "net": [], "org": [], "edu": []], "sd": ["com": [], "net": [], "org": [], "edu": [], "med": [], "tv": [], "gov": [], "info": []], "se": ["a": [], "ac": [], "b": [], "bd": [], "brand": [], "c": [], "d": [], "e": [], "f": [], "fh": [], "fhsk": [], "fhv": [], "g": [], "h": [], "i": [], "k": [], "komforb": [], "kommunalforbund": [], "komvux": [], "l": [], "lanbib": [], "m": [], "n": [], "naturbruksgymn": [], "o": [], "org": [], "p": [], "parti": [], "pp": [], "press": [], "r": [], "s": [], "t": [], "tm": [], "u": [], "w": [], "x": [], "y": [], "z": []], "sg": ["com": [], "net": [], "org": [], "gov": [], "edu": [], "per": []], "sh": ["com": [], "net": [], "gov": [], "org": [], "mil": []], "si": [], "sj": [], "sk": [], "sl": ["com": [], "net": [], "edu": [], "gov": [], "org": []], "sm": [], "sn": ["art": [], "com": [], "edu": [], "gouv": [], "org": [], "perso": [], "univ": []], "so": ["com": [], "net": [], "org": []], "sr": [], "st": ["co": [], "com": [], "consulado": [], "edu": [], "embaixada": [], "gov": [], "mil": [], "net": [], "org": [], "principe": [], "saotome": [], "store": []], "su": [], "sv": ["com": [], "edu": [], "gob": [], "org": [], "red": []], "sx": ["gov": []], "sy": ["edu": [], "gov": [], "net": [], "mil": [], "com": [], "org": []], "sz": ["co": [], "ac": [], "org": []], "tc": [], "td": [], "tel": [], "tf": [], "tg": [], "th": ["ac": [], "co": [], "go": [], "in": [], "mi": [], "net": [], "or": []], "tj": ["ac": [], "biz": [], "co": [], "com": [], "edu": [], "go": [], "gov": [], "int": [], "mil": [], "name": [], "net": [], "nic": [], "org": [], "test": [], "web": []], "tk": [], "tl": ["gov": []], "tm": ["com": [], "co": [], "org": [], "net": [], "nom": [], "gov": [], "mil": [], "edu": []], "tn": ["com": [], "ens": [], "fin": [], "gov": [], "ind": [], "intl": [], "nat": [], "net": [], "org": [], "info": [], "perso": [], "tourism": [], "edunet": [], "rnrt": [], "rns": [], "rnu": [], "mincom": [], "agrinet": [], "defense": [], "turen": []], "to": ["com": [], "gov": [], "net": [], "org": [], "edu": [], "mil": []], "tr": ["com": [], "info": [], "biz": [], "net": [], "org": [], "web": [], "gen": [], "tv": [], "av": [], "dr": [], "bbs": [], "name": [], "tel": [], "gov": [], "bel": [], "pol": [], "mil": [], "k12": [], "edu": [], "kep": [], "nc": ["gov": []]], "tt": ["co": [], "com": [], "org": [], "net": [], "biz": [], "info": [], "pro": [], "int": [], "coop": [], "jobs": [], "mobi": [], "travel": [], "museum": [], "aero": [], "name": [], "gov": [], "edu": []], "tv": [], "tw": ["edu": [], "gov": [], "mil": [], "com": [], "net": [], "org": [], "idv": [], "game": [], "ebiz": [], "club": [], "xn--zf0ao64a": [], "xn--uc0atv": [], "xn--czrw28b": []], "tz": ["ac": [], "co": [], "go": [], "hotel": [], "info": [], "me": [], "mil": [], "mobi": [], "ne": [], "or": [], "sc": [], "tv": []], "ua": ["com": [], "edu": [], "gov": [], "in": [], "net": [], "org": [], "cherkassy": [], "cherkasy": [], "chernigov": [], "chernihiv": [], "chernivtsi": [], "chernovtsy": [], "ck": [], "cn": [], "cr": [], "crimea": [], "cv": [], "dn": [], "dnepropetrovsk": [], "dnipropetrovsk": [], "dominic": [], "donetsk": [], "dp": [], "if": [], "ivano-frankivsk": [], "kh": [], "kharkiv": [], "kharkov": [], "kherson": [], "khmelnitskiy": [], "khmelnytskyi": [], "kiev": [], "kirovograd": [], "km": [], "kr": [], "krym": [], "ks": [], "kv": [], "kyiv": [], "lg": [], "lt": [], "lugansk": [], "lutsk": [], "lv": [], "lviv": [], "mk": [], "mykolaiv": [], "nikolaev": [], "od": [], "odesa": [], "odessa": [], "pl": [], "poltava": [], "rivne": [], "rovno": [], "rv": [], "sb": [], "sebastopol": [], "sevastopol": [], "sm": [], "sumy": [], "te": [], "ternopil": [], "uz": [], "uzhgorod": [], "vinnica": [], "vinnytsia": [], "vn": [], "volyn": [], "yalta": [], "zaporizhzhe": [], "zaporizhzhia": [], "zhitomir": [], "zhytomyr": [], "zp": [], "zt": []], "ug": ["co": [], "or": [], "ac": [], "sc": [], "go": [], "ne": [], "com": [], "org": []], "uk": ["ac": [], "co": [], "gov": [], "ltd": [], "me": [], "net": [], "nhs": [], "org": [], "plc": [], "police": [], "sch": ["*": []]], "us": ["dni": [], "fed": [], "isa": [], "kids": [], "nsn": [], "ak": ["k12": [], "cc": [], "lib": []], "al": ["k12": [], "cc": [], "lib": []], "ar": ["k12": [], "cc": [], "lib": []], "as": ["k12": [], "cc": [], "lib": []], "az": ["k12": [], "cc": [], "lib": []], "ca": ["k12": [], "cc": [], "lib": []], "co": ["k12": [], "cc": [], "lib": []], "ct": ["k12": [], "cc": [], "lib": []], "dc": ["k12": [], "cc": [], "lib": []], "de": ["k12": [], "cc": []], "fl": ["k12": [], "cc": [], "lib": []], "ga": ["k12": [], "cc": [], "lib": []], "gu": ["k12": [], "cc": [], "lib": []], "hi": ["cc": [], "lib": []], "ia": ["k12": [], "cc": [], "lib": []], "id": ["k12": [], "cc": [], "lib": []], "il": ["k12": [], "cc": [], "lib": []], "in": ["k12": [], "cc": [], "lib": []], "ks": ["k12": [], "cc": [], "lib": []], "ky": ["k12": [], "cc": [], "lib": []], "la": ["k12": [], "cc": [], "lib": []], "ma": ["k12": ["pvt": [], "chtr": [], "paroch": []], "cc": [], "lib": []], "md": ["k12": [], "cc": [], "lib": []], "me": ["k12": [], "cc": [], "lib": []], "mi": ["k12": [], "cc": [], "lib": [], "ann-arbor": [], "cog": [], "dst": [], "eaton": [], "gen": [], "mus": [], "tec": [], "washtenaw": []], "mn": ["k12": [], "cc": [], "lib": []], "mo": ["k12": [], "cc": [], "lib": []], "ms": ["k12": [], "cc": [], "lib": []], "mt": ["k12": [], "cc": [], "lib": []], "nc": ["k12": [], "cc": [], "lib": []], "nd": ["cc": [], "lib": []], "ne": ["k12": [], "cc": [], "lib": []], "nh": ["k12": [], "cc": [], "lib": []], "nj": ["k12": [], "cc": [], "lib": []], "nm": ["k12": [], "cc": [], "lib": []], "nv": ["k12": [], "cc": [], "lib": []], "ny": ["k12": [], "cc": [], "lib": []], "oh": ["k12": [], "cc": [], "lib": []], "ok": ["k12": [], "cc": [], "lib": []], "or": ["k12": [], "cc": [], "lib": []], "pa": ["k12": [], "cc": [], "lib": []], "pr": ["k12": [], "cc": [], "lib": []], "ri": ["k12": [], "cc": [], "lib": []], "sc": ["k12": [], "cc": [], "lib": []], "sd": ["cc": [], "lib": []], "tn": ["k12": [], "cc": [], "lib": []], "tx": ["k12": [], "cc": [], "lib": []], "ut": ["k12": [], "cc": [], "lib": []], "vi": ["k12": [], "cc": [], "lib": []], "vt": ["k12": [], "cc": [], "lib": []], "va": ["k12": [], "cc": [], "lib": []], "wa": ["k12": [], "cc": [], "lib": []], "wi": ["k12": [], "cc": [], "lib": []], "wv": ["cc": []], "wy": ["k12": [], "cc": [], "lib": []]], "uy": ["com": [], "edu": [], "gub": [], "mil": [], "net": [], "org": []], "uz": ["co": [], "com": [], "net": [], "org": []], "va": [], "vc": ["com": [], "net": [], "org": [], "gov": [], "mil": [], "edu": []], "ve": ["arts": [], "co": [], "com": [], "e12": [], "edu": [], "firm": [], "gob": [], "gov": [], "info": [], "int": [], "mil": [], "net": [], "org": [], "rec": [], "store": [], "tec": [], "web": []], "vg": [], "vi": ["co": [], "com": [], "k12": [], "net": [], "org": []], "vn": ["com": [], "net": [], "org": [], "edu": [], "gov": [], "int": [], "ac": [], "biz": [], "info": [], "name": [], "pro": [], "health": []], "vu": ["com": [], "edu": [], "net": [], "org": []], "wf": [], "ws": ["com": [], "net": [], "org": [], "gov": [], "edu": []], "yt": [], "xn--mgbaam7a8h": [], "xn--y9a3aq": [], "xn--54b7fta0cc": [], "xn--90ae": [], "xn--90ais": [], "xn--fiqs8s": [], "xn--fiqz9s": [], "xn--lgbbat1ad8j": [], "xn--wgbh1c": [], "xn--e1a4c": [], "xn--node": [], "xn--qxam": [], "xn--j6w193g": ["xn--55qx5d": [], "xn--wcvs22d": [], "xn--mxtq1m": [], "xn--gmqw5a": [], "xn--od0alg": [], "xn--uc0atv": []], "xn--2scrj9c": [], "xn--3hcrj9c": [], "xn--45br5cyl": [], "xn--h2breg3eve": [], "xn--h2brj9c8c": [], "xn--mgbgu82a": [], "xn--rvc1e0am3e": [], "xn--h2brj9c": [], "xn--mgbbh1a": [], "xn--mgbbh1a71e": [], "xn--fpcrj9c3d": [], "xn--gecrj9c": [], "xn--s9brj9c": [], "xn--45brj9c": [], "xn--xkc2dl3a5ee0h": [], "xn--mgba3a4f16a": [], "xn--mgba3a4fra": [], "xn--mgbtx2b": [], "xn--mgbayh7gpa": [], "xn--3e0b707e": [], "xn--80ao21a": [], "xn--fzc2c9e2c": [], "xn--xkc2al3hye2a": [], "xn--mgbc0a9azcg": [], "xn--d1alf": [], "xn--l1acc": [], "xn--mix891f": [], "xn--mix082f": [], "xn--mgbx4cd0ab": [], "xn--mgb9awbf": [], "xn--mgbai9azgqp6j": [], "xn--mgbai9a5eva00b": [], "xn--ygbi2ammx": [], "xn--90a3ac": ["xn--o1ac": [], "xn--c1avg": [], "xn--90azh": [], "xn--d1at": [], "xn--o1ach": [], "xn--80au": []], "xn--p1ai": [], "xn--wgbl6a": [], "xn--mgberp4a5d4ar": [], "xn--mgberp4a5d4a87g": [], "xn--mgbqly7c0a67fbc": [], "xn--mgbqly7cvafr": [], "xn--mgbpl2fh": [], "xn--yfro4i67o": [], "xn--clchc0ea0b2g2a9gcd": [], "xn--ogbpf8fl": [], "xn--mgbtf8fl": [], "xn--o3cw4h": ["xn--12c1fe0br": [], "xn--12co0c3b4eva": [], "xn--h3cuzk1di": [], "xn--o3cyx2a": [], "xn--m3ch0j3a": [], "xn--12cfi8ixb8l": []], "xn--pgbs0dh": [], "xn--kpry57d": [], "xn--kprw13d": [], "xn--nnx388a": [], "xn--j1amh": [], "xn--mgb2ddes": [], "xxx": [], "ye": ["*": []], "za": ["ac": [], "agric": [], "alt": [], "co": [], "edu": [], "gov": [], "grondar": [], "law": [], "mil": [], "net": [], "ngo": [], "nis": [], "nom": [], "org": [], "school": [], "tm": [], "web": []], "zm": ["ac": [], "biz": [], "co": [], "com": [], "edu": [], "gov": [], "info": [], "mil": [], "net": [], "org": [], "sch": []], "zw": ["ac": [], "co": [], "gov": [], "mil": [], "org": []], "aaa": [], "aarp": [], "abarth": [], "abb": [], "abbott": [], "abbvie": [], "abc": [], "able": [], "abogado": [], "abudhabi": [], "academy": [], "accenture": [], "accountant": [], "accountants": [], "aco": [], "active": [], "actor": [], "adac": [], "ads": [], "adult": [], "aeg": [], "aetna": [], "afamilycompany": [], "afl": [], "africa": [], "agakhan": [], "agency": [], "aig": [], "aigo": [], "airbus": [], "airforce": [], "airtel": [], "akdn": [], "alfaromeo": [], "alibaba": [], "alipay": [], "allfinanz": [], "allstate": [], "ally": [], "alsace": [], "alstom": [], "americanexpress": [], "americanfamily": [], "amex": [], "amfam": [], "amica": [], "amsterdam": [], "analytics": [], "android": [], "anquan": [], "anz": [], "aol": [], "apartments": [], "app": [], "apple": [], "aquarelle": [], "arab": [], "aramco": [], "archi": [], "army": [], "art": [], "arte": [], "asda": [], "associates": [], "athleta": [], "attorney": [], "auction": [], "audi": [], "audible": [], "audio": [], "auspost": [], "author": [], "auto": [], "autos": [], "avianca": [], "aws": [], "axa": [], "azure": [], "baby": [], "baidu": [], "banamex": [], "bananarepublic": [], "band": [], "bank": [], "bar": [], "barcelona": [], "barclaycard": [], "barclays": [], "barefoot": [], "bargains": [], "baseball": [], "basketball": [], "bauhaus": [], "bayern": [], "bbc": [], "bbt": [], "bbva": [], "bcg": [], "bcn": [], "beats": [], "beauty": [], "beer": [], "bentley": [], "berlin": [], "best": [], "bestbuy": [], "bet": [], "bharti": [], "bible": [], "bid": [], "bike": [], "bing": [], "bingo": [], "bio": [], "black": [], "blackfriday": [], "blanco": [], "blockbuster": [], "blog": [], "bloomberg": [], "blue": [], "bms": [], "bmw": [], "bnl": [], "bnpparibas": [], "boats": [], "boehringer": [], "bofa": [], "bom": [], "bond": [], "boo": [], "book": [], "booking": [], "bosch": [], "bostik": [], "boston": [], "bot": [], "boutique": [], "box": [], "bradesco": [], "bridgestone": [], "broadway": [], "broker": [], "brother": [], "brussels": [], "budapest": [], "bugatti": [], "build": [], "builders": [], "business": [], "buy": [], "buzz": [], "bzh": [], "cab": [], "cafe": [], "cal": [], "call": [], "calvinklein": [], "cam": [], "camera": [], "camp": [], "cancerresearch": [], "canon": [], "capetown": [], "capital": [], "capitalone": [], "car": [], "caravan": [], "cards": [], "care": [], "career": [], "careers": [], "cars": [], "cartier": [], "casa": [], "case": [], "caseih": [], "cash": [], "casino": [], "catering": [], "catholic": [], "cba": [], "cbn": [], "cbre": [], "cbs": [], "ceb": [], "center": [], "ceo": [], "cern": [], "cfa": [], "cfd": [], "chanel": [], "channel": [], "charity": [], "chase": [], "chat": [], "cheap": [], "chintai": [], "christmas": [], "chrome": [], "chrysler": [], "church": [], "cipriani": [], "circle": [], "cisco": [], "citadel": [], "citi": [], "citic": [], "city": [], "cityeats": [], "claims": [], "cleaning": [], "click": [], "clinic": [], "clinique": [], "clothing": [], "cloud": [], "club": [], "clubmed": [], "coach": [], "codes": [], "coffee": [], "college": [], "cologne": [], "comcast": [], "commbank": [], "community": [], "company": [], "compare": [], "computer": [], "comsec": [], "condos": [], "construction": [], "consulting": [], "contact": [], "contractors": [], "cooking": [], "cookingchannel": [], "cool": [], "corsica": [], "country": [], "coupon": [], "coupons": [], "courses": [], "credit": [], "creditcard": [], "creditunion": [], "cricket": [], "crown": [], "crs": [], "cruise": [], "cruises": [], "csc": [], "cuisinella": [], "cymru": [], "cyou": [], "dabur": [], "dad": [], "dance": [], "data": [], "date": [], "dating": [], "datsun": [], "day": [], "dclk": [], "dds": [], "deal": [], "dealer": [], "deals": [], "degree": [], "delivery": [], "dell": [], "deloitte": [], "delta": [], "democrat": [], "dental": [], "dentist": [], "desi": [], "design": [], "dev": [], "dhl": [], "diamonds": [], "diet": [], "digital": [], "direct": [], "directory": [], "discount": [], "discover": [], "dish": [], "diy": [], "dnp": [], "docs": [], "doctor": [], "dodge": [], "dog": [], "doha": [], "domains": [], "dot": [], "download": [], "drive": [], "dtv": [], "dubai": [], "duck": [], "dunlop": [], "duns": [], "dupont": [], "durban": [], "dvag": [], "dvr": [], "earth": [], "eat": [], "eco": [], "edeka": [], "education": [], "email": [], "emerck": [], "energy": [], "engineer": [], "engineering": [], "enterprises": [], "epost": [], "epson": [], "equipment": [], "ericsson": [], "erni": [], "esq": [], "estate": [], "esurance": [], "etisalat": [], "eurovision": [], "eus": [], "events": [], "everbank": [], "exchange": [], "expert": [], "exposed": [], "express": [], "extraspace": [], "fage": [], "fail": [], "fairwinds": [], "faith": [], "family": [], "fan": [], "fans": [], "farm": [], "farmers": [], "fashion": [], "fast": [], "fedex": [], "feedback": [], "ferrari": [], "ferrero": [], "fiat": [], "fidelity": [], "fido": [], "film": [], "final": [], "finance": [], "financial": [], "fire": [], "firestone": [], "firmdale": [], "fish": [], "fishing": [], "fit": [], "fitness": [], "flickr": [], "flights": [], "flir": [], "florist": [], "flowers": [], "fly": [], "foo": [], "food": [], "foodnetwork": [], "football": [], "ford": [], "forex": [], "forsale": [], "forum": [], "foundation": [], "fox": [], "free": [], "fresenius": [], "frl": [], "frogans": [], "frontdoor": [], "frontier": [], "ftr": [], "fujitsu": [], "fujixerox": [], "fun": [], "fund": [], "furniture": [], "futbol": [], "fyi": [], "gal": [], "gallery": [], "gallo": [], "gallup": [], "game": [], "games": [], "gap": [], "garden": [], "gbiz": [], "gdn": [], "gea": [], "gent": [], "genting": [], "george": [], "ggee": [], "gift": [], "gifts": [], "gives": [], "giving": [], "glade": [], "glass": [], "gle": [], "global": [], "globo": [], "gmail": [], "gmbh": [], "gmo": [], "gmx": [], "godaddy": [], "gold": [], "goldpoint": [], "golf": [], "goo": [], "goodhands": [], "goodyear": [], "goog": [], "google": [], "gop": [], "got": [], "grainger": [], "graphics": [], "gratis": [], "green": [], "gripe": [], "grocery": [], "group": [], "guardian": [], "gucci": [], "guge": [], "guide": [], "guitars": [], "guru": [], "hair": [], "hamburg": [], "hangout": [], "haus": [], "hbo": [], "hdfc": [], "hdfcbank": [], "health": [], "healthcare": [], "help": [], "helsinki": [], "here": [], "hermes": [], "hgtv": [], "hiphop": [], "hisamitsu": [], "hitachi": [], "hiv": [], "hkt": [], "hockey": [], "holdings": [], "holiday": [], "homedepot": [], "homegoods": [], "homes": [], "homesense": [], "honda": [], "honeywell": [], "horse": [], "hospital": [], "host": [], "hosting": [], "hot": [], "hoteles": [], "hotels": [], "hotmail": [], "house": [], "how": [], "hsbc": [], "hughes": [], "hyatt": [], "hyundai": [], "ibm": [], "icbc": [], "ice": [], "icu": [], "ieee": [], "ifm": [], "ikano": [], "imamat": [], "imdb": [], "immo": [], "immobilien": [], "inc": [], "industries": [], "infiniti": [], "ing": [], "ink": [], "institute": [], "insurance": [], "insure": [], "intel": [], "international": [], "intuit": [], "investments": [], "ipiranga": [], "irish": [], "iselect": [], "ismaili": [], "ist": [], "istanbul": [], "itau": [], "itv": [], "iveco": [], "iwc": [], "jaguar": [], "java": [], "jcb": [], "jcp": [], "jeep": [], "jetzt": [], "jewelry": [], "jio": [], "jlc": [], "jll": [], "jmp": [], "jnj": [], "joburg": [], "jot": [], "joy": [], "jpmorgan": [], "jprs": [], "juegos": [], "juniper": [], "kaufen": [], "kddi": [], "kerryhotels": [], "kerrylogistics": [], "kerryproperties": [], "kfh": [], "kia": [], "kim": [], "kinder": [], "kindle": [], "kitchen": [], "kiwi": [], "koeln": [], "komatsu": [], "kosher": [], "kpmg": [], "kpn": [], "krd": [], "kred": [], "kuokgroup": [], "kyoto": [], "lacaixa": [], "ladbrokes": [], "lamborghini": [], "lamer": [], "lancaster": [], "lancia": [], "lancome": [], "land": [], "landrover": [], "lanxess": [], "lasalle": [], "lat": [], "latino": [], "latrobe": [], "law": [], "lawyer": [], "lds": [], "lease": [], "leclerc": [], "lefrak": [], "legal": [], "lego": [], "lexus": [], "lgbt": [], "liaison": [], "lidl": [], "life": [], "lifeinsurance": [], "lifestyle": [], "lighting": [], "like": [], "lilly": [], "limited": [], "limo": [], "lincoln": [], "linde": [], "link": [], "lipsy": [], "live": [], "living": [], "lixil": [], "llc": [], "loan": [], "loans": [], "locker": [], "locus": [], "loft": [], "lol": [], "london": [], "lotte": [], "lotto": [], "love": [], "lpl": [], "lplfinancial": [], "ltd": [], "ltda": [], "lundbeck": [], "lupin": [], "luxe": [], "luxury": [], "macys": [], "madrid": [], "maif": [], "maison": [], "makeup": [], "man": [], "management": [], "mango": [], "map": [], "market": [], "marketing": [], "markets": [], "marriott": [], "marshalls": [], "maserati": [], "mattel": [], "mba": [], "mckinsey": [], "med": [], "media": [], "meet": [], "melbourne": [], "meme": [], "memorial": [], "men": [], "menu": [], "meo": [], "merckmsd": [], "metlife": [], "miami": [], "microsoft": [], "mini": [], "mint": [], "mit": [], "mitsubishi": [], "mlb": [], "mls": [], "mma": [], "mobile": [], "mobily": [], "moda": [], "moe": [], "moi": [], "mom": [], "monash": [], "money": [], "monster": [], "mopar": [], "mormon": [], "mortgage": [], "moscow": [], "moto": [], "motorcycles": [], "mov": [], "movie": [], "movistar": [], "msd": [], "mtn": [], "mtr": [], "mutual": [], "nab": [], "nadex": [], "nagoya": [], "nationwide": [], "natura": [], "navy": [], "nba": [], "nec": [], "netbank": [], "netflix": [], "network": [], "neustar": [], "new": [], "newholland": [], "news": [], "next": [], "nextdirect": [], "nexus": [], "nfl": [], "ngo": [], "nhk": [], "nico": [], "nike": [], "nikon": [], "ninja": [], "nissan": [], "nissay": [], "nokia": [], "northwesternmutual": [], "norton": [], "now": [], "nowruz": [], "nowtv": [], "nra": [], "nrw": [], "ntt": [], "nyc": [], "obi": [], "observer": [], "off": [], "office": [], "okinawa": [], "olayan": [], "olayangroup": [], "oldnavy": [], "ollo": [], "omega": [], "one": [], "ong": [], "onl": [], "online": [], "onyourside": [], "ooo": [], "open": [], "oracle": [], "orange": [], "organic": [], "origins": [], "osaka": [], "otsuka": [], "ott": [], "ovh": [], "page": [], "panasonic": [], "panerai": [], "paris": [], "pars": [], "partners": [], "parts": [], "party": [], "passagens": [], "pay": [], "pccw": [], "pet": [], "pfizer": [], "pharmacy": [], "phd": [], "philips": [], "phone": [], "photo": [], "photography": [], "photos": [], "physio": [], "piaget": [], "pics": [], "pictet": [], "pictures": [], "pid": [], "pin": [], "ping": [], "pink": [], "pioneer": [], "pizza": [], "place": [], "play": [], "playstation": [], "plumbing": [], "plus": [], "pnc": [], "pohl": [], "poker": [], "politie": [], "porn": [], "pramerica": [], "praxi": [], "press": [], "prime": [], "prod": [], "productions": [], "prof": [], "progressive": [], "promo": [], "properties": [], "property": [], "protection": [], "pru": [], "prudential": [], "pub": [], "pwc": [], "qpon": [], "quebec": [], "quest": [], "qvc": [], "racing": [], "radio": [], "raid": [], "read": [], "realestate": [], "realtor": [], "realty": [], "recipes": [], "red": [], "redstone": [], "redumbrella": [], "rehab": [], "reise": [], "reisen": [], "reit": [], "reliance": [], "ren": [], "rent": [], "rentals": [], "repair": [], "report": [], "republican": [], "rest": [], "restaurant": [], "review": [], "reviews": [], "rexroth": [], "rich": [], "richardli": [], "ricoh": [], "rightathome": [], "ril": [], "rio": [], "rip": [], "rmit": [], "rocher": [], "rocks": [], "rodeo": [], "rogers": [], "room": [], "rsvp": [], "rugby": [], "ruhr": [], "run": [], "rwe": [], "ryukyu": [], "saarland": [], "safe": [], "safety": [], "sakura": [], "sale": [], "salon": [], "samsclub": [], "samsung": [], "sandvik": [], "sandvikcoromant": [], "sanofi": [], "sap": [], "sapo": [], "sarl": [], "sas": [], "save": [], "saxo": [], "sbi": [], "sbs": [], "sca": [], "scb": [], "schaeffler": [], "schmidt": [], "scholarships": [], "school": [], "schule": [], "schwarz": [], "science": [], "scjohnson": [], "scor": [], "scot": [], "search": [], "seat": [], "secure": [], "security": [], "seek": [], "select": [], "sener": [], "services": [], "ses": [], "seven": [], "sew": [], "sex": [], "sexy": [], "sfr": [], "shangrila": [], "sharp": [], "shaw": [], "shell": [], "shia": [], "shiksha": [], "shoes": [], "shop": [], "shopping": [], "shouji": [], "show": [], "showtime": [], "shriram": [], "silk": [], "sina": [], "singles": [], "site": [], "ski": [], "skin": [], "sky": [], "skype": [], "sling": [], "smart": [], "smile": [], "sncf": [], "soccer": [], "social": [], "softbank": [], "software": [], "sohu": [], "solar": [], "solutions": [], "song": [], "sony": [], "soy": [], "space": [], "spiegel": [], "sport": [], "spot": [], "spreadbetting": [], "srl": [], "srt": [], "stada": [], "staples": [], "star": [], "starhub": [], "statebank": [], "statefarm": [], "statoil": [], "stc": [], "stcgroup": [], "stockholm": [], "storage": [], "store": [], "stream": [], "studio": [], "study": [], "style": [], "sucks": [], "supplies": [], "supply": [], "support": [], "surf": [], "surgery": [], "suzuki": [], "swatch": [], "swiftcover": [], "swiss": [], "sydney": [], "symantec": [], "systems": [], "tab": [], "taipei": [], "talk": [], "taobao": [], "target": [], "tatamotors": [], "tatar": [], "tattoo": [], "tax": [], "taxi": [], "tci": [], "tdk": [], "team": [], "tech": [], "technology": [], "telecity": [], "telefonica": [], "temasek": [], "tennis": [], "teva": [], "thd": [], "theater": [], "theatre": [], "tiaa": [], "tickets": [], "tienda": [], "tiffany": [], "tips": [], "tires": [], "tirol": [], "tjmaxx": [], "tjx": [], "tkmaxx": [], "tmall": [], "today": [], "tokyo": [], "tools": [], "top": [], "toray": [], "toshiba": [], "total": [], "tours": [], "town": [], "toyota": [], "toys": [], "trade": [], "trading": [], "training": [], "travel": [], "travelchannel": [], "travelers": [], "travelersinsurance": [], "trust": [], "trv": [], "tube": [], "tui": [], "tunes": [], "tushu": [], "tvs": [], "ubank": [], "ubs": [], "uconnect": [], "unicom": [], "university": [], "uno": [], "uol": [], "ups": [], "vacations": [], "vana": [], "vanguard": [], "vegas": [], "ventures": [], "verisign": [], "versicherung": [], "vet": [], "viajes": [], "video": [], "vig": [], "viking": [], "villas": [], "vin": [], "vip": [], "virgin": [], "visa": [], "vision": [], "vista": [], "vistaprint": [], "viva": [], "vivo": [], "vlaanderen": [], "vodka": [], "volkswagen": [], "volvo": [], "vote": [], "voting": [], "voto": [], "voyage": [], "vuelos": [], "wales": [], "walmart": [], "walter": [], "wang": [], "wanggou": [], "warman": [], "watch": [], "watches": [], "weather": [], "weatherchannel": [], "webcam": [], "weber": [], "website": [], "wed": [], "wedding": [], "weibo": [], "weir": [], "whoswho": [], "wien": [], "wiki": [], "williamhill": [], "win": [], "windows": [], "wine": [], "winners": [], "wme": [], "wolterskluwer": [], "woodside": [], "work": [], "works": [], "world": [], "wow": [], "wtc": [], "wtf": [], "xbox": [], "xerox": [], "xfinity": [], "xihuan": [], "xin": [], "xn--11b4c3d": [], "xn--1ck2e1b": [], "xn--1qqw23a": [], "xn--30rr7y": [], "xn--3bst00m": [], "xn--3ds443g": [], "xn--3oq18vl8pn36a": [], "xn--3pxu8k": [], "xn--42c2d9a": [], "xn--45q11c": [], "xn--4gbrim": [], "xn--55qw42g": [], "xn--55qx5d": [], "xn--5su34j936bgsg": [], "xn--5tzm5g": [], "xn--6frz82g": [], "xn--6qq986b3xl": [], "xn--80adxhks": [], "xn--80aqecdr1a": [], "xn--80asehdb": [], "xn--80aswg": [], "xn--8y0a063a": [], "xn--9dbq2a": [], "xn--9et52u": [], "xn--9krt00a": [], "xn--b4w605ferd": [], "xn--bck1b9a5dre4c": [], "xn--c1avg": [], "xn--c2br7g": [], "xn--cck2b3b": [], "xn--cg4bki": [], "xn--czr694b": [], "xn--czrs0t": [], "xn--czru2d": [], "xn--d1acj3b": [], "xn--eckvdtc9d": [], "xn--efvy88h": [], "xn--estv75g": [], "xn--fct429k": [], "xn--fhbei": [], "xn--fiq228c5hs": [], "xn--fiq64b": [], "xn--fjq720a": [], "xn--flw351e": [], "xn--fzys8d69uvgm": [], "xn--g2xx48c": [], "xn--gckr3f0f": [], "xn--gk3at1e": [], "xn--hxt814e": [], "xn--i1b6b1a6a2e": [], "xn--imr513n": [], "xn--io0a7i": [], "xn--j1aef": [], "xn--jlq61u9w7b": [], "xn--jvr189m": [], "xn--kcrx77d1x4a": [], "xn--kpu716f": [], "xn--kput3i": [], "xn--mgba3a3ejt": [], "xn--mgba7c0bbn0a": [], "xn--mgbaakc7dvf": [], "xn--mgbab2bd": [], "xn--mgbb9fbpob": [], "xn--mgbca7dzdo": [], "xn--mgbi4ecexp": [], "xn--mgbt3dhd": [], "xn--mk1bu44c": [], "xn--mxtq1m": [], "xn--ngbc5azd": [], "xn--ngbe9e0a": [], "xn--ngbrx": [], "xn--nqv7f": [], "xn--nqv7fs00ema": [], "xn--nyqy26a": [], "xn--otu796d": [], "xn--p1acf": [], "xn--pbt977c": [], "xn--pssy2u": [], "xn--q9jyb4c": [], "xn--qcka1pmc": [], "xn--rhqv96g": [], "xn--rovu88b": [], "xn--ses554g": [], "xn--t60b56a": [], "xn--tckwe": [], "xn--tiq49xqyj": [], "xn--unup4y": [], "xn--vermgensberater-ctb": [], "xn--vermgensberatung-pwb": [], "xn--vhquv": [], "xn--vuq861b": [], "xn--w4r85el8fhu5dnra": [], "xn--w4rs40l": [], "xn--xhq521b": [], "xn--zfr164b": [], "xperia": [], "xyz": [], "yachts": [], "yahoo": [], "yamaxun": [], "yandex": [], "yodobashi": [], "yoga": [], "yokohama": [], "you": [], "youtube": [], "yun": [], "zappos": [], "zara": [], "zero": [], "zip": [], "zippo": [], "zone": [], "zuerich": []];

	private host;
	private subdomain;
	private domain;
	private suffix;

	private dns;



	/**
	 * Construct
	 *
	 * @param string $host Host.
	 * @param bool $www Strip www.
	 * @return void Nothing.
	 */
	public function __construct(string host, const bool www=false) -> void {
		array parsed = [];

		let this->host = parsed["host"];
		let this->subdomain = parsed["subdomain"];
		let this->domain = parsed["domain"];
		let this->suffix = parsed["suffix"];
	}


	/**
	 * Parse Host
	 *
	 * Try to tease the hostname out of any arbitrary
	 * string, which might be the hostname, a URL, or
	 * something else.
	 *
	 * @param string $host Host.
	 * @return string|bool Host or false.
	 */
	public static function parseHost(string host) -> string | bool {
		// Try to parse it the easy way.
		var tmp = self::parseUrl(host, PHP_URL_HOST);
		if (!empty tmp) {
			let host = tmp;
		}
		// Or the hard way?
		else {
			let host = Strings::trim(host);

			// Cut off the path, if any.
			var start = Strings::strpos(host, "/");
			if (false !== start) {
				let host = Strings::substr(host, 0, start);
			}

			// Cut off the query, if any.
			let start = Strings::strpos(host, "?");
			if (false !== start) {
				let host = Strings::substr(host, 0, start);
			}

			// Cut off credentials, if any.
			let start = Strings::strpos(host, "@");
			if (false !== start) {
				let host = Strings::substr(host, start + 1, null);
			}

			// Is this an IPv6 address?
			if (filter_var(host, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
				let host = IPs::niceIp(host, true);
			}
			else {
				// Pluck an IP out of brackets.
				let start = strpos(host, "[");
				var end = strpos(host, "]");
				if ((0 === start) && false !== end) {
					let host = Strings::substr(host, 1, end - 1);
					let host = IPs::niceIp(host, true);
				}
				// Chop off the port, if any.
				else {
					let start = Strings::strpos(host, ":");
					if (false !== start) {
						let host = Strings::substr(host, 0, start);
					}
				}
			}

			// If it is empty or invalid, there is nothing we can do.
			if (empty host) {
				return false;
			}

			// Convert to ASCII if possible.
			let host = explode(".", host);
			if (defined("INTL_IDNA_VARIANT_UTS46")) {
				let host = idn_to_ascii(host, 0, INTL_IDNA_VARIANT_UTS46);
			}
			else {
				let host = idn_to_ascii(host);
			}
			let host = implode(".", host);

			// Lowercase it.
			let host = Strings::strtolower(host, false);

			// Get rid of trailing periods.
			let host = ltrim(host, ".");
			let host = rtrim(host, ".");
		}

		// Liberate IPv6 from its walls.
		if (0 === strpos(host, "[")) {
			let host = str_replace(["[", "]"], "", host);
			let host = IPs::niceIp(host, true);
		}

		// Is this an IP address? If so, we're done!
		if (filter_var(host, FILTER_VALIDATE_IP)) {
			return host;
		}

		// Look for illegal characters. At this point we should
		// only have nice and safe ASCII.
		if (preg_match("/[^a-z\d\-\.]/u", host)) {
			return false;
		}

		array parts = (array) explode(".", host);
		var v;
		for v in parts {
			// Gotta have length, and can't start or end with a dash.
			if (
				empty v ||
				(0 === strpos(v, "-")) ||
				("-" === substr(v, -1))
			) {
				return false;
			}
		}

		return implode(".", parts);
	}

		/**
	 * Parse Host Parts
	 *
	 * Break a host down into subdomain, domain, and
	 * suffix parts.
	 *
	 * @param string $host Host.
	 * @return array|bool Parts or false.
	 */
	public static function parseHostParts(string host) -> array | bool {
		// Tease out the hostname.
		let host = self::parseHost(host);
		if (empty host) {
			return false;
		}

		array out = [
			"host": null,
			"subdomain": null,
			"domain": null,
			"suffix": null
		];

		// If this is an IP, we don't have to do anything else.
		if (filter_var(host, FILTER_VALIDATE_IP)) {
			let out["host"] = host;
			let out["domain"] = host;
			return out;
		}

		// Now the hard part. See if any parts of the host
		// correspond to a registered suffix.
		array suffixes = self::suffixes;
		array suffix = [];
		array parts = (array) explode(".", host);
		let parts = array_reverse(parts);

		var k, part;
		for k, part in parts {
			// Override rule.
			if (isset(suffixes[part]["!"])) {
				break;
			}

			// A match.
			if (isset(suffixes[part])) {
				array_unshift(suffix, part);
				let suffixes = suffixes[part];
				unset(parts[k]);
				continue;
			}

			// A wildcard.
			if (isset(suffixes["*"])) {
				array_unshift(suffix, part);
				let suffixes = suffixes["*"];
				unset(parts[k]);
				continue;
			}

			// We're done.
			break;
		}

		// The suffix can't be all there is.
		if (!count(parts)) {
			return false;
		}

		// The domain.
		let parts = array_reverse(parts);
		let out["domain"] = array_pop(parts);

		// The subdomain.
		if (count(parts)) {
			let out["subdomain"] = implode(".", parts);
		}

		// The suffix.
		if (count(suffix)) {
			let out["suffix"] = implode(".", suffix);
		}

		let out["host"] = host;
		return out;
	}

	/**
	 * Strip Leading WWW
	 *
	 * The www. subdomain is evil. This removes
	 * it, but only if it is part of the subdomain.
	 *
	 * @return void Nothing.
	 */
	public function stripWww() -> void {
		if (!this->isValid() || null === this->subdomain) {
			return;
		}

		if (
			("www" === this->subdomain) ||
			(0 === strpos(this->subdomain, "www."))
		) {
			let this->subdomain = preg_replace("/^www\.?/u", "", this->subdomain);
			if (empty this->subdomain) {
				let this->subdomain = null;
			}

			let this->host = preg_replace("/^www\./u", "", this->host);
		}
	}



	// -----------------------------------------------------------------
	// Results
	// -----------------------------------------------------------------

	/**
	 * Is Valid
	 *
	 * @param bool $dns Has DNS.
	 * @return bool True/false.
	 */
	public function isValid(const bool dns=false) -> bool {
		return (
			(null !== this->host) &&
			(!dns || this->hasDns())
		);
	}

	/**
	 * Is Fully Qualified Domain Name
	 *
	 * @return bool True/false.
	 */
	public function isFqdn() -> bool {
		return (
			this->isValid() &&
			(("string" === typeof this->suffix) || this->isIp(false))
		);
	}

	/**
	 * Is IP
	 *
	 * @param bool $restricted Allow restricted.
	 * @return bool True/false.
	 */
	public function isIp(const bool restricted=true) -> bool {
		if (!this->isValid()) {
			return false;
		}

		if (restricted) {
			return !!filter_var(this->host, FILTER_VALIDATE_IP);
		}

		return !!filter_var(
			this->host,
			FILTER_VALIDATE_IP,
			FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
		);
	}

	/**
	 * Has DNS
	 *
	 * @return bool True/false.
	 */
	public function hasDns() -> bool {
		if (null === this->dns) {
			if (!this->isFqdn()) {
				let this->dns = false;
			}
			elseif (this->isIp()) {
				let this->dns = this->isIp(false);
			}
			else {
				let this->dns = !!filter_var(
					gethostbyname(this->host . "."),
					FILTER_VALIDATE_IP,
					FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
				);
			}
		}

		return this->dns;
	}

	/**
	 * Is ASCII
	 *
	 * @return bool True/false.
	 */
	public function isAscii() -> bool {
		if (!this->isValid()) {
			return false;
		}

		return !this->isUnicode();
	}

	/**
	 * Is Unicode
	 *
	 * @return bool True/false.
	 */
	public function isUnicode() -> bool {
		if (
			!this->isValid() ||
			this->isIp()
		) {
			return false;
		}

		return (self::toUnicode(this->host) !== this->host);
	}



	// -----------------------------------------------------------------
	// Getters
	// -----------------------------------------------------------------

	/**
	 * Cast to String
	 *
	 * @return string Phone number.
	 */
	public function __toString() {
		return this->isValid() ? this->host : "";
	}

	/**
	 * Get Data
	 *
	 * @param bool $unicode Unicode.
	 * @return array|bool Host data or false.
	 */
	public function getData(const bool unicode=false) -> array | bool {
		if (!this->isValid()) {
			return false;
		}

		return [
			"host": this->getHost(unicode),
			"subdomain": this->getSubdomain(unicode),
			"domain": this->getDomain(unicode),
			"suffix": this->getSuffix(unicode)
		];
	}

	/**
	 * Get Host
	 *
	 * @param bool $unicode Unicode.
	 * @return string|null Host.
	 */
	public function getHost(const bool unicode) {
		if (
			unicode &&
			!empty this->host
		) {
			return self::toUnicode(this->host);
		}

		return this->host;
	}

	/**
	 * Get Subdomain
	 *
	 * @param bool $unicode Unicode.
	 * @return string|null Subdomain.
	 */
	public function getSubdomain(const bool unicode) {
		if (
			unicode &&
			!empty this->subdomain
		) {
			return self::toUnicode(this->subdomain);
		}

		return this->subdomain;
	}

	/**
	 * Get Domain
	 *
	 * @param bool $unicode Unicode.
	 * @return string|null Domain.
	 */
	public function getDomain(const bool unicode) {
		if (
			unicode &&
			!empty this->domain
		) {
			return self::toUnicode(this->domain);
		}

		return this->domain;
	}

	/**
	 * Get Suffix
	 *
	 * @param bool $unicode Unicode.
	 * @return string|null Suffix.
	 */
	public function getSuffix(const bool unicode) {
		if (
			unicode &&
			!empty this->suffix
		) {
			return self::toUnicode(this->suffix);
		}

		return this->suffix;
	}



	// -----------------------------------------------------------------
	// Helpers
	// -----------------------------------------------------------------

	/**
	 * Parse URL
	 *
	 * @see {http://php.net/manual/en/function.parse-url.php#114817}
	 * @see {https://github.com/jeremykendall/php-domain-parser/}
	 *
	 * @param string $url URL.
	 * @param int $component Component.
	 * @return mixed Array, Component, or Null.
	 */
	public static function parseUrl(string url, const int $component = -1) -> array | string | null {
		let url = Strings::trim(url);

		// Before we start, let's fix scheme-agnostic URLs.
		let url = preg_replace("#^:?//#", "https://", url);

		// If an IPv6 address is passed on its own, we
		// need to shove it in brackets.
		if (filter_var(url, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
			let url = "[" . url . "]";
		}

		// The trick is to urlencode (most) parts before passing
		// them to the real parse_url().
		string encoded = (string) preg_replace_callback(
			"%([a-zA-Z][a-zA-Z0-9+\-.]*)?(:?//)?([^:/@?&=#\[\]]+)%usD",
			[__CLASS__, "_parseUrlCallback"],
			url
		);

		// Before getting the real answer, make sure
		// there is a scheme, otherwise PHP will assume
		// all there is is a path, which is stupid.
		if (PHP_URL_SCHEME !== component) {
			var test = parse_url(encoded, PHP_URL_SCHEME);
			if (empty test) {
				let encoded = "blobfolio://" . encoded;
			}
		}

		var parts = parse_url(encoded, component);

		// And now decode what we've been giving. Let's
		// also take a moment to translate Unicode hosts
		// to ASCII.
		if (("string" === typeof parts) && (PHP_URL_SCHEME !== component)) {
			let parts = str_replace(" ", "+", urldecode(parts));

			if (PHP_URL_HOST === component) {
				// Fix Unicode.
				let parts = explode(".", parts);
				if (defined("INTL_IDNA_VARIANT_UTS46")) {
					let parts = (string) idn_to_ascii(
						parts,
						0,
						INTL_IDNA_VARIANT_UTS46
					);
				}
				else {
					let parts = (string) idn_to_ascii(parts);
				}
				let parts = implode(".", parts);

				// Lowercase it.
				let parts = Strings::strtolower(parts, false);

				// Get rid of trailing periods.
				let parts = ltrim(parts, ".");
				let parts = rtrim(parts, ".");

				// Standardize IPv6 formatting.
				if (0 === strpos(parts, "[")) {
					let parts = str_replace(["[", "]"], "", parts);
					let parts = IPs::niceIp(parts, true);
					let parts = "[" . parts . "]";
				}
			}
		}
		elseif ("array" === typeof parts) {
			var k, v;
			for k, v in parts {
				if ("string" !== typeof v) {
					continue;
				}

				if ("scheme" !== k) {
					let parts[k] = str_replace(" ", "+", urldecode(v));
				}
				// Remove our pretend scheme.
				elseif ("blobfolio" === v) {
					unset(parts[k]);
					continue;
				}

				if ("host" === k) {
					// Fix Unicode.
					let parts[k] = explode(".", parts[k]);
					if (defined("INTL_IDNA_VARIANT_UTS46")) {
						let parts[k] = (string) idn_to_ascii(
							parts[k],
							0,
							INTL_IDNA_VARIANT_UTS46
						);
					}
					else {
						let parts[k] = (string) idn_to_ascii(parts[k]);
					}
					let parts[k] = implode(".", parts[k]);

					// Lowercase it.
					let parts[k] = Strings::strtolower(parts[k], false);

					// Get rid of trailing periods.
					let parts[k] = ltrim(parts[k], ".");
					let parts[k] = rtrim(parts[k], ".");

					// Standardize IPv6 formatting.
					if (0 === strpos(parts[k], "[")) {
						let parts[k] = str_replace(["[", "]"], "", parts[k]);
						let parts[k] = IPs::niceIp(parts[k], true);
						let parts[k] = "[" . parts[k] . "]";
					}
				}
			}
		}

		return parts;
	}

	/**
	 * To Unicode
	 *
	 * @param string $value Value.
	 * @return string|null Value.
	 */
	private static function toUnicode(var value) -> string | null {
		if (!empty value && ("string" === typeof value)) {
			let value = explode(".", value);
			if (defined("INTL_IDNA_VARIANT_UTS46")) {
				let value = idn_to_utf8(value, 0, INTL_IDNA_VARIANT_UTS46);
			}
			else {
				let value = idn_to_utf8(value);
			}
			return implode(".", value);
		}

		return value;
	}

	/**
	 * Parse URL Callback.
	 *
	 * @param array $matches Matches.
	 * @return string Replacement.
	 */
	private static function _parseUrlCallback(array matches) -> string {
		return matches[1] . matches[2] . urldecode(matches[3]);
	}
}