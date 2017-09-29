function sha1(e) {
function h(b, a) {
var c, d, e, f, g;
e = b & 2147483648;
f = a & 2147483648;
c = b & 1073741824;
d = a & 1073741824;
g = (b & 1073741823) + (a & 1073741823);
return c & d ? g ^ 2147483648 ^ e ^ f: c | d ? g & 1073741824 ? g ^ 3221225472 ^ e ^ f: g ^ 1073741824 ^ e ^ f: g ^ e ^ f
}
function k(b, a, c, d, e, f, g) {
b = h(b, h(h(a & c | ~a & d, e), g));
return h(b << f | b >>> 32 - f, a)
}
function l(b, a, c, d, e, f, g) {
b = h(b, h(h(a & d | c & ~d, e), g));
return h(b << f | b >>> 32 - f, a)
}
function m(b, a, d, c, e, f, g) {
b = h(b, h(h(a ^ d ^ c, e), g));
return h(b << f | b >>> 32 - f, a)
}
function n(b, a, d, c, e, f, g) {
b = h(b, h(h(d ^ (a | ~c), e), g));
return h(b << f | b >>> 32 - f, a)
}
function p(b) {
var a = "",
d = "",
c;
for (c = 0; 3 >= c; c++) d = b >>> 8 * c & 255,
d = "0" + d.toString(16),
a += d.substr(d.length - 2, 2);
return a
}
var v = e.toString(16),
f = [],
q,
r,
t,
u,
b,
a,
c,
d;
e = function(b) {
b = b.toString();
b = b.replace(/\r\n/g, "\n");
for (var a = "",
d = 0; d < b.length; d++) {
var c = b.charCodeAt(d);
128 > c ? a += String.fromCharCode(c) : (127 < c && 2048 > c ? a += String.fromCharCode(c >> 6 | 192) : (a += String.fromCharCode(c >> 12 | 224), a += String.fromCharCode(c >> 6 & 63 | 128)), a += String.fromCharCode(c & 63 | 128))
}
return a
} (e);
f = function(b) {
var a, c = b.length;
a = c + 8;
for (var d = 16 * ((a - a % 64) / 64 + 1), e = Array(d - 1), f = 0, g = 0; g < c;) a = (g - g % 4) / 4,
f = g % 4 * 8,
e[a] |= b.charCodeAt(g) << f,
g++;
a = (g - g % 4) / 4;
e[a] |= 128 << g % 4 * 8;
e[d - 2] = c << 3;
e[d - 1] = c >>> 29;
return e
} (e);
b = 1732584193;
a = 4023233417;
c = 2562383102;
d = 271733878;
for (e = 0; e < f.length; e += 16) q = b,
r = a,
t = c,
u = d,
b = k(b, a, c, d, f[e + 0], 7, 3614090360),
d = k(d, b, a, c, f[e + 1], 12, 3905402710),
c = k(c, d, b, a, f[e + 2], 17, 606105819),
a = k(a, c, d, b, f[e + 3], 22, 3250441966),
b = k(b, a, c, d, f[e + 4], 7, 4118548399),
d = k(d, b, a, c, f[e + 5], 12, 1200080426),
c = k(c, d, b, a, f[e + 6], 17, 2821735955),
a = k(a, c, d, b, f[e + 7], 22, 4249261313),
b = k(b, a, c, d, f[e + 8], 7, 1770035416),
d = k(d, b, a, c, f[e + 9], 12, 2336552879),
c = k(c, d, b, a, f[e + 10], 17, 4294925233),
a = k(a, c, d, b, f[e + 11], 22, 2304563134),
b = k(b, a, c, d, f[e + 12], 7, 1804603682),
d = k(d, b, a, c, f[e + 13], 12, 4254626195),
c = k(c, d, b, a, f[e + 14], 17, 2792965006),
a = k(a, c, d, b, f[e + 15], 22, 1236535329),
b = l(b, a, c, d, f[e + 1], 5, 4129170786),
d = l(d, b, a, c, f[e + 6], 9, 3225465664),
c = l(c, d, b, a, f[e + 11], 14, 643717713),
a = l(a, c, d, b, f[e + 0], 20, 3921069994),
b = l(b, a, c, d, f[e + 5], 5, 3593408605),
d = l(d, b, a, c, f[e + 10], 9, 38016083),
c = l(c, d, b, a, f[e + 15], 14, 3634488961),
a = l(a, c, d, b, f[e + 4], 20, 3889429448),
b = l(b, a, c, d, f[e + 9], 5, 568446438),
d = l(d, b, a, c, f[e + 14], 9, 3275163606),
c = l(c, d, b, a, f[e + 3], 14, 4107603335),
a = l(a, c, d, b, f[e + 8], 20, 1163531501),
b = l(b, a, c, d, f[e + 13], 5, 2850285829),
d = l(d, b, a, c, f[e + 2], 9, 4243563512),
c = l(c, d, b, a, f[e + 7], 14, 1735328473),
a = l(a, c, d, b, f[e + 12], 20, 2368359562),
b = m(b, a, c, d, f[e + 5], 4, 4294588738),
d = m(d, b, a, c, f[e + 8], 11, 2272392833),
c = m(c, d, b, a, f[e + 11], 16, 1839030562),
a = m(a, c, d, b, f[e + 14], 23, 4259657740),
b = m(b, a, c, d, f[e + 1], 4, 2763975236),
d = m(d, b, a, c, f[e + 4], 11, 1272893353),
c = m(c, d, b, a, f[e + 7], 16, 4139469664),
a = m(a, c, d, b, f[e + 10], 23, 3200236656),
b = m(b, a, c, d, f[e + 13], 4, 681279174),
d = m(d, b, a, c, f[e + 0], 11, 3936430074),
c = m(c, d, b, a, f[e + 3], 16, 3572445317),
a = m(a, c, d, b, f[e + 6], 23, 76029189),
b = m(b, a, c, d, f[e + 9], 4, 3654602809),
d = m(d, b, a, c, f[e + 12], 11, 3873151461),
c = m(c, d, b, a, f[e + 15], 16, 530742520),
a = m(a, c, d, b, f[e + 2], 23, 3299628645),
b = n(b, a, c, d, f[e + 0], 6, 4096336452),
d = n(d, b, a, c, f[e + 7], 10, 1126891415),
c = n(c, d, b, a, f[e + 14], 15, 2878612391),
a = n(a, c, d, b, f[e + 5], 21, 4237533241),
b = n(b, a, c, d, f[e + 12], 6, 1700485571),
d = n(d, b, a, c, f[e + 3], 10, 2399980690),
c = n(c, d, b, a, f[e + 10], 15, 4293915773),
a = n(a, c, d, b, f[e + 1], 21, 2240044497),
b = n(b, a, c, d, f[e + 8], 6, 1873313359),
d = n(d, b, a, c, f[e + 15], 10, 4264355552),
c = n(c, d, b, a, f[e + 6], 15, 2734768916),
a = n(a, c, d, b, f[e + 13], 21, 1309151649),
b = n(b, a, c, d, f[e + 4], 6, 4149444226),
d = n(d, b, a, c, f[e + 11], 10, 3174756917),
c = n(c, d, b, a, f[e + 2], 15, 718787259),
a = n(a, c, d, b, f[e + 9], 21, 3951481745),
b = h(b, q),
a = h(a, r),
c = h(c, t),
d = h(d, u);
return (p(b) + p(a) + p(c) + p(d)).toLowerCase() + v
};