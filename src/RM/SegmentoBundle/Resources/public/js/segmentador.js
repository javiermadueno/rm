/**
 *
 *
 */

var Segmentador;

Segmentador = Segmentador || {};


Segmentador.Token =
{
    Operador: 'Operador',
    Segmento: 'Segmento'
};


Segmentador.Lexer = function () {
    var expression = '',
        length = 0,
        index = 0,  //Indice global
        marker = 0,
        Token = {
            Operador: 'Operador',
            Segmento: 'Segmento'
        };

    function peekNextChar() {
        var indice = index;
        return ((indice < length ) ? expression.charAt(indice) : '\x00');
    }

    function getNextChar() {
        var ch = '\x00',
            indice = index;

        if (indice < length) {
            ch = expression.charAt(indice);
            index = index + 1;
        }

        return ch;
    }

    function isWhiteSpace(ch) {
        return (ch === '\u0009') || (ch === ' ') || (ch === '\u00A0');
    }

    function isLetter(ch) {
        return (ch >= 'a' && ch <= 'z') || (ch >= 'A' && ch <= 'Z');
    }

    function isDigit(ch) {
        return !isNaN(ch) && (ch >= '0') && (ch <= '9');
    }

    function createToken(type, value) {
        return {
            type: type,
            value: value,
            start: marker,
            end: index - 1
        };
    }

    function skipSpaces() {
        var ch;

        while (index < length) {
            ch = peekNextChar();
            if (!isWhiteSpace(ch)) {
                break;
            }
            getNextChar();
        }
    }


    function scanOperator() {
        var ch = peekNextChar();

        if ('&|!()'.indexOf(ch) >= 0) {
            return createToken(Token.Operador, getNextChar());
        }

        return undefined;
    }

    function isSegmentStart(ch) {
        return isDigit(ch);
    }

    function isSegmentPart(ch) {
        return isSegmentStart(ch) || isDigit(ch);
    }


    function scanSegment() {
        var ch, segmento;

        ch = peekNextChar();
        if (!isSegmentStart(ch)) {
            return undefined;
        }

        segmento = getNextChar();

        while (true) {
            ch = peekNextChar();

            if (!isSegmentPart(ch)) {
                break;
            }

            segmento += getNextChar();
        }

        return createToken(Token.Segmento, segmento);
    }


    function reset(str) {

        if (typeof str === 'undefined') {
            expression = '';
            index = 0;
            length = 0;
            return;
        }

        expression = str;
        length = str.length;
        index = 0;
    }

    function next() {
        var token;

        skipSpaces();
        if (index >= length) {
            return undefined;
        }

        marker = index;

        token = scanOperator();
        if (typeof token !== 'undefined') {
            return token;
        }

        token = scanSegment();
        if (typeof token !== 'undefined') {
            return token;
        }

        throw new SyntaxError('Comienzo de Token no valido ' + peekNextChar());
    }

    function peek() {
        var token, indice;

        indice = index;
        try {
            token = next();
            delete token.start;
            delete token.end;
        }
        catch (e) {
            token = undefined;
        }

        index = indice;


        return token;
    }


    return {
        reset: reset,
        next: next,
        peek: peek
    };


};

Segmentador.Parser = function () {

    var lexer = new Segmentador.Lexer(),
        Token = Segmentador.Token;

    function matchOp(token, op) {
        return typeof token !== 'undefined' &&
        token.type === Token.Operador &&
        token.value === op;
    }

    function parseUnary() {
        var token, expr;

        token = lexer.peek();
        if (matchOp(token, '!')) {
            token = lexer.next();
            expr = parseUnary();

            return {
                'Negado': {
                    operador: token.value,
                    expresion: expr
                }
            };
        }

        return parsePrimary();
    }

    function parsePrimary() {
        var token, expr;

        token = lexer.peek();

        if (typeof token === 'undefined') {
            throw new SyntaxError('Expresion no valida');
        }

        if (token.type === Token.Segmento) {
            token = lexer.next();
            return {
                'Segmento': token.value
            };
        }


        if (matchOp(token, '(')) {
            lexer.next();
            expr = parseAssignment();
            token = lexer.next();
            if (!matchOp(token, ')')) {
                throw new SyntaxError('Se espera ")"');
            }

            return {
                'Expresion': expr
            };
        }


        throw new SyntaxError('No se puede procesar el token ' + token.value);
    }


    function parseAssignment() {
        var expr, token;

        expr = parseAdditive();

        return expr;
    }


    function parseAdditive() {
        var token, expr;

        expr = parseUnary();
        token = lexer.peek();

        while (matchOp(token, '&') || matchOp(token, '|')) {
            token = lexer.next();
            expr = {
                'Binary': {
                    operador: token.value,
                    left: expr,
                    right: parseUnary()
                }
            };

            token = lexer.peek();
        }

        return expr;
    }


    function parse(expresion) {
        var expr, token;

        lexer.reset(expresion);
        expr = parseAssignment();

        token = lexer.next();
        if (typeof token !== 'undefined') {
            throw new SyntaxError('Expresión incorrecta ' + token.value);
        }

        return {
            'Expresion': expr
        };

    }

    return {
        parse: parse
    };
}

Segmentador.Context = function () {
    var Constant, Variables, Funciones;

    Constant = {
        'fecha': new Date().toISOString()
    };

    Variables = {};

    Funciones = {
        'in': '{"ls": {"$in": [%id_segmento%]}}',
        notin: '{"ls": {"$nin": [%id_segmento%]}}',
        and: '{"$and": [%condiciones%]}',
        or: '{"$or": [%condiciones%]}',
        fecha: '"_id.fIni": { $lt: ISODate("%fecha%")},"fFin": { $gt: ISODate("%fecha%")}'
    };

    return {
        Constant: Constant,
        Variables: Variables,
        Funciones: Funciones
    };

}

Segmentador.Evaluator = function (ctx) {

    var parser = new Segmentador.Parser();
    var context = (arguments.length < 1) ? new Segmentador.Context : merge_options(new Segmentador.Context, ctx);

    /**
     * Overwrites obj1's values with obj2's and adds obj2's if non existent in obj1
     * @param obj1
     * @param obj2
     * @returns obj3 a new object based on obj1 and obj2
     */
    function merge_options(obj1, obj2) {
        var obj3 = {};
        for (var attrname in obj1) {
            obj3[attrname] = obj1[attrname];
        }
        for (var attrname in obj2) {
            obj3[attrname] = obj2[attrname];
        }
        return obj3;
    }

    // function parseEn() {
    // 	if(typeof context.Variables.en === 'undefined') {
    // 		return undefined;
    // 	}

    // 	context.Variables.en = context.Variables.en.replace(/,\s*$/, "");

    // 	return context.Funciones['in'].replace('%id_segmento%', context.Variables.en);
    // }

    // function parseNin() {
    // 	if (typeof context.Variables.nin === 'undefined') {
    // 		return undefined;
    // 	}

    // 	context.Variables.nin = context.Variables.nin.replace(/,\s*$/, "");

    // 	return context.Funciones.nin.replace('%id_segmento%', context.Variables.nin);
    // }

    // function parseAnd(){

    // 	var condiciones = '',
    // 	en, nin;

    // 	en = parseEn();
    // 	nin = parseNin();

    // 	if(typeof en !== 'undefined') {
    // 		condiciones += en + ',';
    // 	}

    // 	if(typeof nin !== 'undefined'){
    // 		condiciones += nin + ',';
    // 	}

    // 	condiciones = condiciones.replace(/,\s*$/, "");

    // 	borraVariables();

    // 	return context.Funciones.and.replace('%condiciones%', condiciones);
    // }

    // function parseOr() {
    // 	var condiciones ='', en, nin;

    // 	en = parseEn();
    // 	nin = parseNin();

    // 	if(typeof en !== 'undefined') {
    // 		condiciones += en + ',';
    // 	}

    // 	if(typeof nin !== 'undefined') {
    // 		condiciones += nin + ',';
    // 	}

    // 	condiciones = condiciones.replace(/,\s*$/, "");
    // 	borraVariables();

    // 	return context.Funciones.or.replace('%condiciones%', condiciones);
    // }

    // function borraVariables() {
    // 	context.Variables = {};
    // }


    function exec(node) {
        var left, right, expr, en, nin;

        if (node.hasOwnProperty('Expresion')) {
            return exec(node.Expresion);
        }

        if (node.hasOwnProperty('Segmento')) {
            context.Variables.en = (typeof context.Variables.en === 'undefined' ) ? '' : context.Variables.en;
            context.Variables.en += parseInt(node.Segmento) + ',';
            return context.Funciones['in'].replace('%id_segmento%', parseInt(node.Segmento));
        }

        if (node.hasOwnProperty('Negado')) {
            node = node.Negado;
            segmento = parseInt(node.expresion.Segmento);
            context.Variables.nin = (typeof context.Variables.nin === 'undefined' ) ? '' : context.Variables.nin;
            context.Variables.nin += segmento + ',';
            return context.Funciones.notin.replace('%id_segmento%', segmento);
        }

        if (node.hasOwnProperty('Binary')) {
            node = node.Binary;
            left = exec(node.left);
            right = exec(node.right);


            switch (node.operador) {
                case '&':
                    //return parseAnd();
                    return context.Funciones.and.replace('%condiciones%', left + ',' + right);
                    break;
                case '|':
                    //return parseOr();
                    return context.Funciones.or.replace('%condiciones%', left + ',' + right);
                    break;

                default:
                    throw new SyntaxError('Operador desconocido: ' + node.operador);
            }
        }

    }

    function evaluate(expr) {
        var tree = parser.parse(expr);
        return exec(tree);
    }

    function addDate(condicion) {
        var obj = JSON.parse(condicion) || {};
        obj['_id.fIni'] = {'$lt': 'ISODate("%fecha%")'.replace('%fecha%', context.Constant.fecha)};
        obj['fFin'] = {'$gt': 'ISODate("%fecha%")'.replace('%fecha%', context.Constant.fecha)};

        obj['_id.fIni'] = {'$lt': {'$date': new Date(context.Constant.fecha)}};
        obj['fFin'] = {'$gt': {'$date': new Date(context.Constant.fecha)}};

        return JSON.stringify(obj);
    }

    return {
        evaluate: evaluate
    };
}
