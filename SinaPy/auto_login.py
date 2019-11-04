#!/usr/bin/env python
#coding=utf-8
#encoding:utf-8

import urllib
import urllib2
import cookielib
import base64
import re
import json
import hashlib
import os
import rsa
import binascii
import fateadm_api
import sys



txt_path = os.getcwd()
txt_save_path = '/data/webapp/zou/zou_script/SinaPy/'+'cookie.txt'
filename=txt_save_path
cookiejar = cookielib.LWPCookieJar(filename)
cookie_support = urllib2.HTTPCookieProcessor(cookiejar)
opener = urllib2.build_opener(cookie_support, urllib2.HTTPHandler)
urllib2.install_opener(opener)

parameters = {
    'entry': 'weibo',
    'callback': 'sinaSSOController.preloginCallBack',
    'su': 'TGVuZGZhdGluZyU0MHNpbmEuY29t',
    'rsakt': 'mod',
    'checkpin': '1',
    'client': 'ssologin.js(v1.4.5)',
    '_': '1362560902427'
}

postdata = {
    'entry': 'weibo',
    'gateway': '1',
    'from': '',
    'savestate': '7',
    'useticket': '1',
    'pagerefer': '',
    'vsnf': '1',
    'su': '',
    'service': 'miniblog',
    'servertime': '',
    'nonce': '',
    'pwencode': 'rsa2',
    'rsakv': '',
    'sp': '',
    'encoding': 'UTF-8',
    'prelt': '27',
    'url': 'http://www.weibo.com/ajaxlogin.php?framelogin=1&callback=parent.sinaSSOController.feedBackUrlCallBack',
    'returntype': 'META'
}

def get_servertime():
    url = 'http://login.sina.com.cn/sso/prelogin.php?' + urllib.urlencode(parameters)
    data = urllib2.urlopen(url).read()
    p = re.compile('\((.*)\)')
    try:
        json_data = p.search(data).group(1)
        data = json.loads(json_data)
        print data
        servertime = str(data['servertime'])
        nonce = data['nonce']
        pubkey = data['pubkey']
        rsakv = data['rsakv']
        pcid = data['pcid']
        showpin = data['showpin']
        return servertime, nonce, pubkey, rsakv, pcid, showpin
    except:
        print 'Get severtime error!'
        return None

def get_pwd(pwd, servertime, nonce, pubkey):
    #先创建一个rsa公钥，公钥的两个参数新浪微博都给了是固定值，不过给的都是16进制的字符串，
    #第一个是登录第一步中的pubkey，第二个是js加密文件中的‘10001’。
    #这两个值需要先从16进制转换成10进制，不过也可以写死在代码里。我就把‘10001’直接写死为65537
    rsaPublickey = int(pubkey, 16)
    key = rsa.PublicKey(rsaPublickey, 65537) #创建公钥
    message = str(servertime) + '\t' + str(nonce) + '\n' + str(pwd) #拼接明文 js加密文件中得到
    passwd = rsa.encrypt(message, key) #加密
    passwd = binascii.b2a_hex(passwd)  #将加密信息转换为16进制
    return passwd

def get_user(username):
    username_ = urllib.quote(username)
    username = base64.encodestring(username_)[:-1]
    return username

def login(username, pwd):

    url = 'http://login.sina.com.cn/sso/login.php?client=ssologin.js(v1.4.5)'
    try:
        servertime, nonce, pubkey, rsakv, pcid, showpin = get_servertime()
    except:
        return
    global postdata

    # $imgurl = "http://login.sina.com.cn/cgi/pin.php?r=$rand&s=0&p=".$redis->get(self::REDIS_QIAN.
    # 'sina_pcid');

    # door = fateadm_api.TestFunc('http://test.zhunzha.com/pin.png')
    postdata['servertime'] = servertime
    postdata['nonce'] = nonce
    postdata['rsakv'] = rsakv
    # postdata['door'] = door
    postdata['su'] = get_user(username)
    postdata['sp'] = get_pwd(pwd, servertime, nonce, pubkey)

    if showpin == 1:
        imgurl = "http://login.sina.com.cn/cgi/pin.php?r=$rand&s=0&p="+pcid
        door = fateadm_api.TestFunc(imgurl)
        # print door
        postdata['door'] = door
        postdata['pcid'] = pcid

    print  postdata
    postdata = urllib.urlencode(postdata)
    headers = {'User-Agent': 'Mozilla/5.0 (X11; Linux i686; rv:8.0) Gecko/20100101 Firefox/8.0'}

    req = urllib2.Request(
        url=url,
        data=postdata,
        # headers=headers
    )
    result = urllib2.urlopen(req)
    text = result.read()
    # print cookiejar._cookies.values()
    print text
    p = re.compile('location\.replace\(\'(.*?)\'\)')
    try:
        login_url = p.search(text).group(1)#如果没有异常返回，说明此时已自动登录，之后只需设置url和data就可以post或者直接get，
                                           #注意不要在request中不要设置header，这是因为cookie也是header的一部分，如果设置header会导致没有cookie，也就没有登录
        print login_url
        urllib2.urlopen(login_url)
        print cookiejar._cookies.values()
        cookiejar.save(ignore_discard=True, ignore_expires=True)
        print "登录成功!"
    except:
        txt_path = os.getcwd()
        txt_save_path = '/data/webapp/hdcj/hdcj_script/SinaPy/'+'cookie.txt'
        f=open(txt_save_path, "r+")
        f.truncate()   #清空文件
        f.write('null')
        print 'Login error!'


user = sys.argv[1]
password = sys.argv[2]
account = user.split('=')[1]
account_pass = password.split('=')[1]
login(account,account_pass)
